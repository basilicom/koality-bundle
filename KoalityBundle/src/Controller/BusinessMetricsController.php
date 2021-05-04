<?php

namespace Basilicom\KoalityBundle\Controller;

use Basilicom\KoalityBundle\Checks\DebugModeEnabledCheck;
use Basilicom\KoalityBundle\Checks\MaintenanceWorkerRunningCheck;
use Basilicom\KoalityBundle\Checks\NewCartsPerTimeIntervalCheck;
use Basilicom\KoalityBundle\Checks\OrdersPerTimeIntervalCheck;
use Basilicom\KoalityBundle\DependencyInjection\Configuration;
use Leankoala\HealthFoundation\HealthFoundation as HealthFoundation;
use Leankoala\HealthFoundation\Result\Format\Koality\KoalityFormat as KoalityFormat;
use Pimcore\Controller\FrontendController;
use Pimcore\Maintenance\Executor;
use Pimcore\Maintenance\ExecutorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\RouterInterface;

class BusinessMetricsController extends FrontendController
{
    private HealthFoundation $healthFoundation;
    private KoalityFormat  $koalityFormatter;
    private ExecutorInterface $maintenanceExecutor;
    private RouterInterface $router;

    private DebugModeEnabledCheck $debugModeEnabledCheck;
    private MaintenanceWorkerRunningCheck $maintenanceWorkerRunningCheck;
    private NewCartsPerTimeIntervalCheck $newCartsPerTimeIntervalCheck;

    private array $config;

    public function __construct(
        $config,
        DebugModeEnabledCheck $debugModeEnabledCheck,
        MaintenanceWorkerRunningCheck $maintenanceWorkerRunningCheck,
        NewCartsPerTimeIntervalCheck $newCartsPerTimeIntervalCheck,
        Executor $maintenanceExecutor,
        RouterInterface $router

    ) {
        $this->config = $config;
        $this->debugModeEnabledCheck = $debugModeEnabledCheck;
        $this->maintenanceWorkerRunningCheck = $maintenanceWorkerRunningCheck;
        $this->newCartsPerTimeIntervalCheck = $newCartsPerTimeIntervalCheck;
        $this->maintenanceExecutor = $maintenanceExecutor;
        $this->router = $router;
        $this->init();
    }


    public function businessChecksAction()
    {
        $token = $this->config[Configuration::TOKEN][Configuration::SECRET];
        if (!empty($token)) {
            $this->router->generate('business_metrics', ['token' => $token]);
        }

        $response = new Response();
        $response->setContent($this->runBusinessChecks());
        $response->headers->set('Content-Type', 'application/health+json');
        $response->send();

        return $response;
    }

    private function runBusinessChecks()
    {
        $availableBundles = $this->get("kernel")->getBundles();

        if (array_key_exists("PimcoreEcommerceFrameworkBundle", $availableBundles))
        {
            if ($this->config[Configuration::ORDERS_PER_TIME_INTERVAL_CHECK][Configuration::ENABLE] === true) {
                $this->runOrdersPerTimeIntervalCheck(new OrdersPerTimeIntervalCheck());
            }
        }

        if ($this->config[Configuration::DEBUG_MODE_ENABLED_CHECK][Configuration::ENABLE] === true) {
            $this->runDebugModeEnabledCheck();
        }
        if ($this->config[Configuration::MAINTENANCE_WORKER_RUNNING_CHECK][Configuration::ENABLE] === true) {
            $this->runMaintenanceWorkerRunningCheck();
        }
        if ($this->config[Configuration::NEW_CARTS_PER_TIME_INTERVAL_CHECK][Configuration::ENABLE] === true) {
            $this->runNewCartsPerTimeIntervalCheck();
        }

        $runResult = $this->healthFoundation->runHealthCheck();

        return json_encode($this->koalityFormatter->handle($runResult, false), JSON_PRETTY_PRINT);
    }

    private function runOrdersPerTimeIntervalCheck($check)
    {
        $hours = $this->config[Configuration::ORDERS_PER_TIME_INTERVAL_CHECK][Configuration::HOURS];
        $threshold = $this->config[Configuration::ORDERS_PER_TIME_INTERVAL_CHECK][Configuration::THRESHOLD];
        $check->init($hours, $threshold);

        $this->healthFoundation->registerCheck(
            $check,
            'orders_per_time_interval',
            'Shows count of orders during the last ' . $hours . ' hour(s)'
        );
    }

    private function runNewCartsPerTimeIntervalCheck()
    {
        $hours = $this->config[Configuration::ORDERS_PER_TIME_INTERVAL_CHECK][Configuration::HOURS];
        $this->newCartsPerTimeIntervalCheck->init($hours);

        $this->healthFoundation->registerCheck(
            $this->newCartsPerTimeIntervalCheck,
            'new_carts_per_time_interval',
            'Shows count of new carts during the last ' . $hours . ' hour(s)'
        );
    }

    private function runDebugModeEnabledCheck()
    {
        $this->healthFoundation->registerCheck(
            $this->debugModeEnabledCheck,
            'debug_mode_enabled_check',
            'Indicates whether debug mode is enabled.'
        );
    }

    private function runMaintenanceWorkerRunningCheck()
    {
        $this->maintenanceWorkerRunningCheck->init($this->maintenanceExecutor);

        $this->healthFoundation->registerCheck(
            $this->maintenanceWorkerRunningCheck,
            'maintenance_worker_running_check',
            'Indicates whether maintenance jobs where running within last hour'
        );
    }

    private function init()
    {
        $this->healthFoundation = new HealthFoundation();
        $this->koalityFormatter = new KoalityFormat();

    }
}
