import * as React from 'react';

interface SliderProps {
  value: number;
  min?: number;
  max?: number;
  step?: number;
  label?: string;
  onChange: (value: number) => void;
}

export function Slider({ value, min = 0, max = 1, step = 0.01, label, onChange }: SliderProps) {
  const handleChange = (event: React.ChangeEvent<HTMLInputElement>) => {
    onChange(Number(event.target.value));
  };

  return (
    <label className="flex w-full flex-col gap-1">
      {label ? <span className="text-xs text-slate-400">{label}</span> : null}
      <input
        type="range"
        value={value}
        min={min}
        max={max}
        step={step}
        onChange={handleChange}
        className="h-2 w-full cursor-pointer appearance-none rounded-full bg-slate-700"
      />
      <span className="font-mono text-xs text-emerald-300">{value.toFixed(2)}</span>
    </label>
  );
}
