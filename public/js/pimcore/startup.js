pimcore.registerNS("pimcore.plugin.StarfruitPaymentBundle");

pimcore.plugin.StarfruitPaymentBundle = Class.create({

    initialize: function () {
        document.addEventListener(pimcore.events.pimcoreReady, this.pimcoreReady.bind(this));
    },

    pimcoreReady: function (e) {
        // alert("StarfruitPaymentBundle ready!");
    }
});

var StarfruitPaymentBundlePlugin = new pimcore.plugin.StarfruitPaymentBundle();
