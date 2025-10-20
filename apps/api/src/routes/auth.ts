import { Router } from 'express';
import { StatusCodes } from 'http-status-codes';
import bcrypt from 'bcryptjs';
import { z } from 'zod';
import { UserModel } from '../models/user.js';
import { generateToken } from '../utils/auth.js';

const router = Router();

const registerSchema = z.object({
  email: z.string().email(),
  password: z.string().min(8),
  displayName: z.string().min(2),
});

router.post('/register', async (req, res) => {
  const parsed = registerSchema.safeParse(req.body);
  if (!parsed.success) {
    return res.status(StatusCodes.BAD_REQUEST).json({ errors: parsed.error.flatten() });
  }

  const { email, password, displayName } = parsed.data;
  const existing = await UserModel.findOne({ email }).lean();
  if (existing) {
    return res.status(StatusCodes.CONFLICT).json({ message: 'Email already registered' });
  }

  const passwordHash = await bcrypt.hash(password, 12);
  const user = await UserModel.create({ email, passwordHash, displayName });
  const token = generateToken({ userId: user._id.toString() });

  return res.status(StatusCodes.CREATED).json({
    token,
    user: {
      id: user._id.toString(),
      email: user.email,
      displayName: user.displayName,
    },
  });
});

const loginSchema = z.object({
  email: z.string().email(),
  password: z.string().min(8),
});

router.post('/login', async (req, res) => {
  const parsed = loginSchema.safeParse(req.body);
  if (!parsed.success) {
    return res.status(StatusCodes.BAD_REQUEST).json({ errors: parsed.error.flatten() });
  }

  const { email, password } = parsed.data;
  const user = await UserModel.findOne({ email });
  if (!user) {
    return res.status(StatusCodes.UNAUTHORIZED).json({ message: 'Invalid credentials' });
  }

  const valid = await bcrypt.compare(password, user.passwordHash);
  if (!valid) {
    return res.status(StatusCodes.UNAUTHORIZED).json({ message: 'Invalid credentials' });
  }

  const token = generateToken({ userId: user._id.toString() });
  return res.json({
    token,
    user: {
      id: user._id.toString(),
      email: user.email,
      displayName: user.displayName,
    },
  });
});

export default router;
