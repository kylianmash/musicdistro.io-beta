import type { Clip } from '@musicdistro/types';
import { WaveFile } from 'wavefile';
import lamejs from 'lamejs';

export type ExportFormat = 'wav' | 'mp3';

export async function exportMixdown(clips: Clip[], format: ExportFormat = 'wav'): Promise<Blob> {
  if (!clips.length) {
    throw new Error('No clips to export');
  }

  const sampleRate = 44100;
  const maxEnd = clips.reduce((acc, clip) => Math.max(acc, clip.end), 0);
  const length = Math.ceil(maxEnd * sampleRate);
  const context = new OfflineAudioContext(2, length, sampleRate);

  for (const clip of clips) {
    if (clip.kind !== 'audio') continue;
    const response = await fetch(clip.fileId);
    const arrayBuffer = await response.arrayBuffer();
    const audioBuffer = await context.decodeAudioData(arrayBuffer.slice(0));
    const source = context.createBufferSource();
    source.buffer = audioBuffer;
    source.connect(context.destination);
    source.start(clip.start);
  }

  const rendered = await context.startRendering();
  const interleaved = interleave(rendered.getChannelData(0), rendered.getChannelData(1));

  if (format === 'mp3') {
    return encodeMp3(rendered, sampleRate);
  }

  const wav = new WaveFile();
  wav.fromScratch(2, sampleRate, '32f', interleaved);
  const buffer = wav.toBuffer();
  return new Blob([buffer], { type: 'audio/wav' });
}

function interleave(left: Float32Array, right: Float32Array) {
  const length = left.length + right.length;
  const result = new Float32Array(length);
  let index = 0;
  for (let i = 0; i < left.length; i++) {
    result[index++] = left[i];
    result[index++] = right[i] ?? left[i];
  }
  return result;
}

function encodeMp3(buffer: AudioBuffer, sampleRate: number) {
  const left = buffer.getChannelData(0);
  const right = buffer.getChannelData(1);
  const mp3encoder = new lamejs.Mp3Encoder(2, sampleRate, 192);
  const blockSize = 1152;
  const mp3Data: Int8Array[] = [];

  for (let i = 0; i < left.length; i += blockSize) {
    const leftChunk = convertBuffer(left.subarray(i, i + blockSize));
    const rightChunk = convertBuffer(right.subarray(i, i + blockSize));
    const mp3buf = mp3encoder.encodeBuffer(leftChunk, rightChunk);
    if (mp3buf.length > 0) {
      mp3Data.push(mp3buf);
    }
  }

  const remaining = mp3encoder.flush();
  if (remaining.length > 0) {
    mp3Data.push(remaining);
  }

  return new Blob(mp3Data, { type: 'audio/mpeg' });
}

function convertBuffer(input: Float32Array) {
  const output = new Int16Array(input.length);
  for (let i = 0; i < input.length; i++) {
    const s = Math.max(-1, Math.min(1, input[i]));
    output[i] = s < 0 ? s * 0x8000 : s * 0x7fff;
  }
  return output;
}
