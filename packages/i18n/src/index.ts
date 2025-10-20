export type Locale = 'en' | 'fr' | 'es';

const messages: Record<Locale, Record<string, string>> = {
  en: {
    'nav.studio': 'Music Studio',
    'studio.title': 'MusicDistro Studio',
    'studio.subtitle': 'Compose, record, and mix directly from your browser.',
    'studio.newTrack': 'Add Track',
    'studio.import': 'Import Audio',
    'studio.play': 'Play',
    'studio.stop': 'Stop',
    'studio.export': 'Export Mixdown',
    'studio.mixer': 'Mixer',
  },
  fr: {
    'nav.studio': 'Studio Musical',
    'studio.title': 'MusicDistro Studio',
    'studio.subtitle': 'Composez, enregistrez et mixez directement dans votre navigateur.',
    'studio.newTrack': 'Ajouter une piste',
    'studio.import': 'Importer un audio',
    'studio.play': 'Lecture',
    'studio.stop': 'Stop',
    'studio.export': 'Exporter le mix',
    'studio.mixer': 'Console',
  },
  es: {
    'nav.studio': 'Estudio Musical',
    'studio.title': 'MusicDistro Studio',
    'studio.subtitle': 'Compón, graba y mezcla directamente en tu navegador.',
    'studio.newTrack': 'Añadir pista',
    'studio.import': 'Importar audio',
    'studio.play': 'Reproducir',
    'studio.stop': 'Detener',
    'studio.export': 'Exportar mezcla',
    'studio.mixer': 'Mezclador',
  },
};

let currentLocale: Locale = 'en';

export function setLocale(locale: Locale) {
  currentLocale = locale;
}

export function t(key: string): string {
  return messages[currentLocale]?.[key] ?? messages.en[key] ?? key;
}
