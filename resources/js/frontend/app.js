import 'lazysizes';

import * as collapsible from './modules/collapsible.js';
import * as dropdown from './modules/dropdown.js';
import * as filter from './modules/filter.js';
import * as history from './modules/history.js';
import * as imagescroll from './modules/imagescroll.js';
import * as imprint from './modules/imprint.js';
import * as menu from './modules/menu.js';
import * as overlay from './modules/overlay.js';
import * as project from './modules/project.js';
import * as swiper from './modules/swiper.js';

// Module scripts run after the document is parsed
[collapsible, dropdown, filter, history, imagescroll, imprint, menu, overlay, project, swiper]
  .forEach((module) => module.init());
