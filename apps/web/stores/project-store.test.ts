import { afterEach, describe, expect, test } from 'vitest';
import { act } from 'react-dom/test-utils';
import { useProjectStore } from './project-store';

afterEach(() => {
  useProjectStore.setState({ tracks: [], clips: [], selectedClipIds: [], selectedTrackId: null });
});

describe('project store', () => {
  test('creates audio track with default settings', () => {
    act(() => {
      useProjectStore.getState().createTrack('audio');
    });
    const track = useProjectStore.getState().tracks[0];
    expect(track).toMatchObject({ type: 'audio', volume: -6, pan: 0 });
  });

  test('adds audio clip and auto-selects it', () => {
    act(() => {
      const track = useProjectStore.getState().createTrack('audio');
      useProjectStore.getState().addAudioClip(track.id, 'https://example.com/audio.wav', 4);
    });
    const state = useProjectStore.getState();
    expect(state.clips).toHaveLength(1);
    expect(state.selectedClipIds).toHaveLength(1);
  });

  test('creates midi clip with instrument inheritance', () => {
    act(() => {
      const track = useProjectStore.getState().createTrack('instrument', 'piano');
      useProjectStore.getState().addMidiClip(track.id, 0, 4);
    });
    const clip = useProjectStore.getState().clips[0];
    expect(clip.kind).toBe('midi');
    if (clip.kind === 'midi') {
      expect(clip.instrument).toBe('piano');
    }
  });
});
