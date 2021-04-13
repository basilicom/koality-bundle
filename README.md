# Basilicom.de Pimcore - Koality Bundle

### General Information
This Pimcore Bundle provides an endpoint for the health-check monitoring plugin from https://www.koality.io/de  
There are 3 Checks already implemented, 2 from the *leankoala/healthfoundation* Bundle, namely **SpaceUsedCheck** and **UptimeCheck**.  
The 3rd Check, **OrdersPerHourCheck** , is Pimcore eCommerce Framework specific and outputs the count of orders during the last hour.
### Usage
    composer require basilicom/koality-bundle
    
Make sure to register the Bundle in *AppKernel*, e.g.
            
    public function registerBundlesToCollection(BundleCollection $collection) {
        $collection->addBundle(new \Basilicom\KoalityBundle\KoalityBundle());
    }

### How the plugin works

Once installed the Bundle will provide JSON formatted Data under the following endpoint 

    yourdomain.tu/koality-status
    
##### Example JSON Output

```
{
    "status": "fail",
    "output": "The health check failed.",
    "checks": {
        "space_used_check": {
            "status": "pass",
            "output": "Enough space left on device. 69% used (\/).",
            "description": "Space used on storage server",
            "observedValue": 0.69,
            "observedUnit": "percent",
            "metricType": "time_series_percent",
            "observedValuePrecision": 2,
            "limit": 0.95,
            "limitType": "max"
        },
        "orders_per_hour_check": {
            "status": "pass",
            "output": "Count of orders during the last hour",
            "description": "Shows count of orders during the last hour",
            "observedValue": 23,
            "observedUnit": "Orders",
            "metricType": "time_series_numeric",
            "observedValuePrecision": 2
        },
        "server_uptime_check": {
            "status": "fail",
            "output": "Servers uptime is too high (2 years 11 days 8 hours 47 minutes)",
            "description": "Shows the server uptime. Gives warning, if Limit is exceeded.",
            "observedValue": 0,
            "metricType": "time_series_percent",
            "observedUnit": "percent"
        }
    }
}
```

### Metrics

The following Metrics are implemented yet

- **SpaceUsedCheck** - this check fails if the amount of space left on device is below 5% .
- **UptimeCheck** - this check fails if the defined uptime is exceeded (1 year per Default).
- **OrdersPerHourCheck** - this check measures the number of orders in the past hour.


### TODO

Add more Pimcore eCommerce Specific Metrics like 
- Minimum number of active products (to detect import failures)
- Add possibility to configure the individual tests (via xml config file?)
- tbc.