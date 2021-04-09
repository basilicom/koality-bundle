# Koality Bundle

### General Information
This Pimcore Bundle provides an endpoint for the health-check monitoring plugin from https://www.koality.io/de  
There are 3 Checks already implemented, 2 from the *leankoala/healthfoundation* Bundle, namely **SpaceUsedCheck** and **ContainerIsRunningCheck**.  
The 3rd Check, *OrdersPerHourCheck* , is Pimcore eCommerce Framework specific and outputs the count of orders during the last hour.
### Usage
    composer require basilicom/koality-bundle
    
Make sure to register the Bundle in *AppKernel*, e.g.
            
    public function registerBundlesToCollection(BundleCollection $collection) {
        $collection->addBundle(new \Basilicom\KoalityBundle\KoalityBundle());
    }

Once installed the Bundle will provide JSON formatted Data under   

    yourdomain.tu/koality-status