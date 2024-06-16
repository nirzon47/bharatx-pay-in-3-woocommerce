const settings = window.wc.wcSettings.getSetting('bharatx_pay_in_3_data', {});
const label =
  window.wp.htmlEntities.decodeEntities(settings.title) ||
  window.wp.i18n.__('BharatX Pay In 3', 'bharatx_pay_in_3');

const Content = () => {
  return window.wp.htmlEntities.decodeEntities(settings.description || '');
};

const Block_Gateway = {
  name: 'bharatx_pay_in_3',
  label: label,
  content: Object(window.wp.element.createElement)(Content, null),
  edit: Object(window.wp.element.createElement)(Content, null),
  canMakePayment: () => true,
  ariaLabel: label,
  supports: {
    features: settings.supports,
  },
};

window.wc.wcBlocksRegistry.registerPaymentMethod(Block_Gateway);
