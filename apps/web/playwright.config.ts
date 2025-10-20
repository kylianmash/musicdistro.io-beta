import type { PlaywrightTestConfig } from '@playwright/test';

const config: PlaywrightTestConfig = {
  webServer: {
    command: 'pnpm dev',
    port: 3000,
    timeout: 120_000,
    reuseExistingServer: !process.env.CI,
  },
  testDir: './tests/e2e',
};

export default config;
