import { test, expect } from '@playwright/test';

test('launch studio timeline', async ({ page }) => {
  await page.goto('/studio');
  await expect(page.locator('text=MusicDistro')).toBeVisible();
  await expect(page.locator('text=Add audio track')).toBeVisible();
});
