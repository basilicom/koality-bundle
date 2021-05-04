# Basilicom.de Pimcore - Koality Bundle

### General Information
This Pimcore Bundle provides 2 different endpoints (one for business-metrics, one for server-metrics) for the health-check monitoring service of https://www.koality.io/de . 
See Metrics Section for a detailed overview of the provided metrics.
### Usage
    composer require basilicom/koality-bundle
    
Make sure to register the Bundle in *AppKernel*, e.g.
            
    public function registerBundlesToCollection(BundleCollection $collection) {
        $collection->addBundle(new \Basilicom\KoalityBundle\KoalityBundle());
    }

### How the plugin works

Once installed the Bundle will provide JSON formatted Data under the following endpoints

    yourdomain.tu/pimcore-koality-bundle-business 
    yourdomain.tu/pimcore-koality-bundle-server
either for Business or Server metrics.

If you've set a secret token the endpoints will look as follows

    yourdomain.tu/pimcore-koality-bundle-business?token=mySecret
    yourdomain.tu/pimcore-koality-bundle-server?token=mySecret

##### Configuration

Add following config section to your config.yaml

```
koality:
    token:
        secret: 'mySecret'
    orders_per_time_interval_check:
        enable: true
        hours: 24
        threshold: 1000
    new_carts_per_time_interval_check:
        enable: true
        hours: 1
    debug_mode_enabled_check:
        enable: true
    maintenance_worker_running_check:
        enable: true
    server_uptime_check:
        enable: true
        time_interval: '7 years'
    space_used_check:
        enable: true
        limit_in_percent: 80
        path_to_container: '/some/path/'
    container_is_running_check:
        enable: true
        container_name: 'test container name'
    
```
    
##### Example JSON Output

```
{
    "status": "fail",
    "output": "The health check failed.",
    "checks": {
        "orders_per_time_interval": {
            "status": "fail",
            "output": "Count of orders during the specified time interval is below threshold of: 1000",
            "description": "Shows count of orders during the last 24 hour(s)",
            "observedValue": 0,
            "observedUnit": "Orders",
            "metricType": "time_series_numeric",
            "observedValuePrecision": 2
        },
        "debug_mode_enabled_check": {
            "status": "fail",
            "output": "Debug mode is ON",
            "description": "Indicates whether debug mode is enabled.",
            "observedValue": 0,
            "metricType": "time_series_percent",
            "observedUnit": "percent"
        },
        "maintenance_worker_running_check": {
            "status": "fail",
            "output": "Maintenance Job wasn't executed within the last hour",
            "description": "Indicates whether maintenance jobs where running within last hour",
            "observedValue": 0,
            "metricType": "time_series_percent",
            "observedUnit": "percent"
        },

        ...

    }
}
```

### Metrics 

The following Metrics are implemented yet

#### Server Metrics

- **SpaceUsedCheck** - this check fails if the amount of space left on device is below de defined limit.
- **UptimeCheck** - this check fails if the defined uptime is exceeded (1 year per Default).
- **ContainerIsRunningCheck** - this check fails if the defined container isn't running.

#### Business (Pimcore) Metrics

- **OrdersPerTimeIntervalCheck** - this check measures the number of new orders during a certain time interval. It will fail, if the provided threshold isn't reached.
- **NewCartsPerTimeIntervalCheck** - this check measures the number of new carts during a certain time interval.
- **MaintenanceWorkerRunningCheck** - this check provides information on whether the maintenance job has been completed during the past hour.
- **DebugModeEnabledCheck** - this check indicates whether debug mode is on.

### TODO

Add more Pimcore eCommerce Specific Metrics like 
- Minimum number of active products (to detect import failures)
- Check for Pimcore Update
- Login attempts during specified time period
- tbc.
