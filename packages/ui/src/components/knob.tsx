import { motion } from 'framer-motion';
import { useCallback, useMemo, useState } from 'react';

interface KnobProps {
  value: number;
  min: number;
  max: number;
  step?: number;
  label: string;
  onChange: (value: number) => void;
}

export function Knob({ value, min, max, step = 0.01, label, onChange }: KnobProps) {
  const [isActive, setActive] = useState(false);
  const percentage = useMemo(() => (value - min) / (max - min), [value, min, max]);

  const handlePointerMove = useCallback(
    (event: PointerEvent) => {
      if (!isActive) return;
      const delta = -event.movementY;
      const range = max - min;
      const next = Math.min(max, Math.max(min, value + (delta / 150) * range));
      const rounded = Math.round(next / step) * step;
      onChange(Number(rounded.toFixed(3)));
    },
    [isActive, max, min, onChange, step, value]
  );

  const handlePointerUp = useCallback(() => {
    setActive(false);
    window.removeEventListener('pointermove', handlePointerMove);
    window.removeEventListener('pointerup', handlePointerUp);
  }, [handlePointerMove]);

  const handlePointerDown = useCallback(
    (event: React.PointerEvent<HTMLDivElement>) => {
      event.preventDefault();
      setActive(true);
      window.addEventListener('pointermove', handlePointerMove);
      window.addEventListener('pointerup', handlePointerUp);
    },
    [handlePointerMove, handlePointerUp]
  );

  return (
    <div className="flex flex-col items-center gap-1 text-xs text-slate-200">
      <motion.div
        onPointerDown={handlePointerDown}
        whileTap={{ scale: 0.94 }}
        className="relative h-16 w-16 cursor-pointer rounded-full bg-gradient-to-br from-slate-800 to-slate-900 shadow-inner"
      >
        <motion.span
          className="absolute left-1/2 top-1/2 block h-6 w-1 origin-bottom -translate-x-1/2 -translate-y-full rounded-sm bg-emerald-400"
          animate={{ rotate: percentage * 270 - 135 }}
          transition={{ type: 'spring', stiffness: 240, damping: 30 }}
        />
      </motion.div>
      <span className="font-mono text-[10px] uppercase tracking-widest text-slate-400">{label}</span>
      <span className="font-mono text-xs text-emerald-300">{value.toFixed(2)}</span>
    </div>
  );
}
