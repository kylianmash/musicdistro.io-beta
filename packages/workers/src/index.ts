export async function generateWaveform(file: ArrayBuffer): Promise<number[]> {
  // Simple RMS-based waveform preview, replace with FFT worker later.
  const context = new OfflineAudioContext(1, 44100, 44100);
  const audioBuffer = await context.decodeAudioData(file.slice(0));
  const samples = audioBuffer.getChannelData(0);
  const bucketSize = Math.floor(samples.length / 512) || 1;
  const waveform: number[] = [];

  for (let i = 0; i < samples.length; i += bucketSize) {
    let sum = 0;
    for (let j = 0; j < bucketSize && i + j < samples.length; j++) {
      const sample = samples[i + j];
      sum += sample * sample;
    }
    waveform.push(Math.sqrt(sum / bucketSize));
  }

  return waveform;
}
