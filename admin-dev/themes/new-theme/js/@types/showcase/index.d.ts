import ShowcaseCard from '@components/showcase-card/showcase-card';

interface ShowcaseExtension {
  extend: (grid: ShowcaseCard) => void;
}

export {ShowcaseCard, ShowcaseExtension};
