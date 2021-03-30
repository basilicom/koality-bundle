pimcore.registerNS("pimcore.plugin.KoalityBundle");

pimcore.plugin.KoalityBundle = Class.create(pimcore.plugin.admin, {
    getClassName: function () {
        return "pimcore.plugin.KoalityBundle";
    },

    initialize: function () {
        pimcore.plugin.broker.registerPlugin(this);
    },

    pimcoreReady: function (params, broker) {
        // alert("KoalityBundle ready!");
    }
});

var KoalityBundlePlugin = new pimcore.plugin.KoalityBundle();
