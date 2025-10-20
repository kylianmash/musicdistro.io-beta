'use client';

import { builtinPlugins } from '@/lib/plugins';

export function PluginDock() {
  return (
    <div className="grid gap-4 md:grid-cols-3">
      {builtinPlugins.map((plugin) => {
        const UI = plugin.createUI?.();
        return (
          <div key={plugin.id} className="rounded-2xl bg-studio-surface/80 p-4 panel-shadow">
            <div className="flex items-center justify-between">
              <h3 className="text-sm font-semibold text-white">{plugin.name}</h3>
              <span className="text-[10px] uppercase tracking-[0.4em] text-emerald-400">{plugin.category}</span>
            </div>
            {UI ? <UI params={plugin.defaultParams} setParam={() => undefined} /> : null}
          </div>
        );
      })}
    </div>
  );
}

export default PluginDock;
