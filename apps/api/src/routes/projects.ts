import { Router } from 'express';
import { StatusCodes } from 'http-status-codes';
import { z } from 'zod';
import { ProjectModel } from '../models/project.js';
import { authenticate, type AuthenticatedRequest } from '../utils/auth.js';
import type { ProjectDocument } from '@musicdistro/types';

const router = Router();

const transportSchema = z.object({
  bpm: z.number(),
  timeSignature: z.tuple([z.number(), z.number()]),
  position: z.number(),
  isPlaying: z.boolean(),
  loop: z.object({ enabled: z.boolean(), start: z.number(), end: z.number() }),
  grid: z.string(),
  metronomeEnabled: z.boolean(),
});

const clipSchema = z.object({
  id: z.string(),
  name: z.string(),
  trackId: z.string(),
  start: z.number(),
  end: z.number(),
  offset: z.number(),
  color: z.string(),
  isLoop: z.boolean().optional(),
  muted: z.boolean().optional(),
  kind: z.enum(['audio', 'midi']),
  fileId: z.string().optional(),
  fadeIn: z.number().optional(),
  fadeOut: z.number().optional(),
  warpMode: z.string().optional(),
  gain: z.number().optional(),
  transpose: z.number().optional(),
  instrument: z.string().optional(),
  notes: z
    .array(z.object({ id: z.string(), pitch: z.number(), velocity: z.number(), start: z.number(), duration: z.number() }))
    .optional(),
});

const trackSchema = z.object({
  id: z.string(),
  name: z.string(),
  type: z.string(),
  color: z.string(),
  order: z.number(),
  armed: z.boolean(),
  muted: z.boolean(),
  solo: z.boolean(),
  volume: z.number(),
  pan: z.number(),
  instrument: z.string().optional(),
  plugins: z.array(z.object({ id: z.string(), type: z.string(), bypassed: z.boolean(), params: z.record(z.number()), presetId: z.string().optional() })),
  automation: z.array(
    z.object({
      id: z.string(),
      parameter: z.string(),
      points: z.array(z.object({ id: z.string(), time: z.number(), value: z.number() })),
    })
  ),
});

const projectSchema = z.object({
  name: z.string().min(1),
  description: z.string().optional(),
  transport: transportSchema,
  tracks: z.array(trackSchema),
  clips: z.array(clipSchema),
});

router.use(authenticate);

router.get('/', async (req: AuthenticatedRequest, res) => {
  const projects = await ProjectModel.find({ userId: req.userId }).sort({ updatedAt: -1 }).lean();
  return res.json({ projects });
});

router.post('/', async (req: AuthenticatedRequest, res) => {
  const parsed = projectSchema.safeParse(req.body);
  if (!parsed.success) {
    return res.status(StatusCodes.BAD_REQUEST).json({ errors: parsed.error.flatten() });
  }

  const payload = parsed.data as ProjectDocument;
  const project = await ProjectModel.create({ ...payload, userId: req.userId });
  return res.status(StatusCodes.CREATED).json({ project });
});

router.get('/:id', async (req: AuthenticatedRequest, res) => {
  const project = await ProjectModel.findOne({ _id: req.params.id, userId: req.userId });
  if (!project) {
    return res.status(StatusCodes.NOT_FOUND).json({ message: 'Project not found' });
  }
  return res.json({ project });
});

router.put('/:id', async (req: AuthenticatedRequest, res) => {
  const parsed = projectSchema.partial().safeParse(req.body);
  if (!parsed.success) {
    return res.status(StatusCodes.BAD_REQUEST).json({ errors: parsed.error.flatten() });
  }

  const project = await ProjectModel.findOneAndUpdate({ _id: req.params.id, userId: req.userId }, parsed.data, {
    new: true,
  });
  if (!project) {
    return res.status(StatusCodes.NOT_FOUND).json({ message: 'Project not found' });
  }
  return res.json({ project });
});

router.delete('/:id', async (req: AuthenticatedRequest, res) => {
  const project = await ProjectModel.findOneAndDelete({ _id: req.params.id, userId: req.userId });
  if (!project) {
    return res.status(StatusCodes.NOT_FOUND).json({ message: 'Project not found' });
  }
  return res.status(StatusCodes.NO_CONTENT).send();
});

export default router;
