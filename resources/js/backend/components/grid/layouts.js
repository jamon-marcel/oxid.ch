/**
 * The project layouts, as columns of stacked cells. A cell has the grid
 * element's position and its ratio (height in % of its width): small boxes
 * are 43:28, tall ones twice as high. Rows (GridRow) and pictograms
 * (LayoutIcon) are drawn from this; no template per layout.
 */
const small = 65.1163;
const tall = 130.2326;

export const layouts = {
  '1': {
    label: 'Ein Bild',
    columns: [{ fr: 1, cells: [{ position: 0, ratio: small }] }],
  },
  '1-1': {
    label: 'Zwei Bilder hoch',
    columns: [
      { fr: 1, cells: [{ position: 0, ratio: tall }] },
      { fr: 1, cells: [{ position: 1, ratio: tall }] },
    ],
  },
  '2-1': {
    label: 'Zwei quer, eines hoch',
    columns: [
      { fr: 1, cells: [{ position: 0, ratio: small }, { position: 1, ratio: small }] },
      { fr: 1, cells: [{ position: 2, ratio: tall }] },
    ],
  },
  '1-2': {
    label: 'Eines hoch, zwei quer',
    columns: [
      { fr: 1, cells: [{ position: 0, ratio: tall }] },
      { fr: 1, cells: [{ position: 1, ratio: small }, { position: 2, ratio: small }] },
    ],
  },
  '2-2': {
    label: 'Vier Bilder',
    columns: [
      { fr: 1, cells: [{ position: 0, ratio: small }, { position: 2, ratio: small }] },
      { fr: 1, cells: [{ position: 1, ratio: small }, { position: 3, ratio: small }] },
    ],
  },
};
