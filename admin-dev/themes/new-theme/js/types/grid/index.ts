import Grid from '@components/grid/grid';

interface GridExtension {
  extend: (grid: Grid) => void;
}

export {Grid, GridExtension};
