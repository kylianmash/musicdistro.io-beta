import 'dotenv/config';

interface EnvConfig {
  port: number;
  mongoUri: string;
  jwtSecret: string;
  corsOrigin: string | RegExp | (string | RegExp)[];
}

const port = Number(process.env.PORT ?? 4000);
const mongoUri = process.env.MONGODB_URI ?? 'mongodb://127.0.0.1:27017/musicdistro';
const jwtSecret = process.env.JWT_SECRET ?? 'dev-secret';
const corsOrigin = process.env.CORS_ORIGIN?.split(',') ?? '*';

export const config: EnvConfig = {
  port,
  mongoUri,
  jwtSecret,
  corsOrigin,
};
