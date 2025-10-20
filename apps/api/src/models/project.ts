import { Schema, model, Types } from 'mongoose';
import type { ProjectDocument as ProjectPayload } from '@musicdistro/types';

export interface ProjectDocument extends ProjectPayload {
  _id: Types.ObjectId;
}

const clipSchema = new Schema(
  {
    id: String,
    name: String,
    trackId: String,
    start: Number,
    end: Number,
    offset: Number,
    color: String,
    isLoop: Boolean,
    muted: Boolean,
    kind: String,
    fileId: String,
    fadeIn: Number,
    fadeOut: Number,
    warpMode: String,
    gain: Number,
    transpose: Number,
    instrument: String,
    notes: [
      {
        id: String,
        pitch: Number,
        velocity: Number,
        start: Number,
        duration: Number,
      },
    ],
  },
  { _id: false }
);

const trackSchema = new Schema(
  {
    id: String,
    name: String,
    type: String,
    color: String,
    order: Number,
    armed: Boolean,
    muted: Boolean,
    solo: Boolean,
    volume: Number,
    pan: Number,
    instrument: String,
    plugins: [
      {
        id: String,
        type: String,
        bypassed: Boolean,
        params: Schema.Types.Mixed,
        presetId: String,
      },
    ],
    automation: [
      {
        id: String,
        parameter: String,
        points: [
          {
            id: String,
            time: Number,
            value: Number,
          },
        ],
      },
    ],
  },
  { _id: false }
);

const projectSchema = new Schema<ProjectDocument>(
  {
    userId: { type: Schema.Types.ObjectId, ref: 'User', required: true, index: true },
    name: { type: String, required: true },
    description: String,
    transport: {
      bpm: Number,
      timeSignature: [Number],
      position: Number,
      isPlaying: Boolean,
      loop: {
        enabled: Boolean,
        start: Number,
        end: Number,
      },
      grid: String,
      metronomeEnabled: Boolean,
    },
    tracks: [trackSchema],
    clips: [clipSchema],
  },
  { timestamps: true }
);

projectSchema.set('toJSON', {
  transform: (_, doc) => {
    const { _id, __v, ...rest } = doc;
    return { id: _id, ...rest };
  },
});

export const ProjectModel = model<ProjectDocument>('Project', projectSchema);
