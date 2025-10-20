import express from 'express';
import cors from 'cors';
import http from 'http';
import mongoose from 'mongoose';
import { Server } from 'socket.io';
import { config } from './config.js';
import authRoutes from './routes/auth.js';
import projectRoutes from './routes/projects.js';

const app = express();

app.use(
  cors({
    origin: config.corsOrigin,
    credentials: true,
  })
);
app.use(express.json({ limit: '15mb' }));

app.get('/health', (_req, res) => {
  res.json({ status: 'ok', uptime: process.uptime() });
});

app.use('/auth', authRoutes);
app.use('/projects', projectRoutes);

const server = http.createServer(app);
const io = new Server(server, {
  cors: {
    origin: config.corsOrigin,
  },
});

io.on('connection', (socket) => {
  socket.on('project:join', (projectId: string) => {
    socket.join(projectId);
  });

  socket.on('project:update', (payload: { projectId: string; snapshot: unknown }) => {
    socket.to(payload.projectId).emit('project:update', payload.snapshot);
  });
});

async function start() {
  try {
    await mongoose.connect(config.mongoUri);
    server.listen(config.port, () => {
      console.log(`MusicDistro API running on port ${config.port}`);
    });
  } catch (error) {
    console.error('Failed to start API server', error);
    process.exit(1);
  }
}

void start();
