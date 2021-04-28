<?php

namespace Basilicom\KoalityBundle\Controller;

use Basilicom\KoalityBundle\Checks\DebugModeEnabledCheck;
use Basilicom\KoalityBundle\Checks\MaintenanceWorkerRunningCheck;
use Basilicom\KoalityBundle\Checks\OrdersPerTimeIntervalCheck;
use Basilicom\KoalityBundle\DependencyInjection\Configuration;
use Leankoala\HealthFoundation\HealthFoundation as HealthFoundation;
use Leankoala\HealthFoundation\Result\Format\Koality\KoalityFormat as KoalityFormat;
use Pimcore\Controller\FrontendController;
use Pimcore\Maintenance\Executor;
use Pimcore\Maintenance\ExecutorInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class BusinessMetricsController extends FrontendController
{
    private HealthFoundation $healthFoundation;
    private KoalityFormat  $koalityFormatter;
    private ExecutorInterface $maintenanceExecutor;

    private OrdersPerTimeIntervalCheck $ordersPerTimeIntervalCheck;
    private DebugModeEnabledCheck $debugModeEnabledCheck;
    private MaintenanceWorkerRunningCheck $maintenanceWorkerRunningCheck;

    private array $config;

    public function __construct(
        $config,
        OrdersPerTimeIntervalCheck $ordersPerTimeIntervalCheck,
        DebugModeEnabledCheck $debugModeEnabledCheck,
        MaintenanceWorkerRunningCheck $maintenanceWorkerRunningCheck,
        Executor $maintenanceExecutor

    ) {
        $this->config = $config;
        $this->ordersPerTimeIntervalCheck = $ordersPerTimeIntervalCheck;
        $this->debugModeEnabledCheck = $debugModeEnabledCheck;
        $this->maintenanceWorkerRunningCheck = $maintenanceWorkerRunningCheck;
        $this->maintenanceExecutor = $maintenanceExecutor;
        $this->init();
    }

    /**
     * @Route("/pimcore-koality-bundle-business")
     */
    public function businessChecksAction()
    {
        $response = new Response();
        $response->setContent($this->runBusinessChecks());
        $response->headers->set('Content-Type', 'application/health+json');
        $response->send();

        return $response;
    }

    private function runBusinessChecks()
    {
        //TODO Überlegen welche Response geliefert wird, wenn keine Metrik aktiviert wurde
        if ($this->config[Configuration::ORDERS_PER_TIME_INTERVAL_CHECK][Configuration::ENABLE] === true) {
            $this->runOrdersPerTimeIntervalCheck();
        }
        if ($this->config[Configuration::DEBUG_MODE_ENABLED_CHECK][Configuration::ENABLE] === true) {
            $this->runDebugModeEnabledCheck();
        }
        if ($this->config[Configuration::MAINTENANCE_WORKER_RUNNING_CHECK][Configuration::ENABLE] === true) {
            $this->runMaintenanceWorkerRunningCheck();
        }

        $runResult = $this->healthFoundation->runHealthCheck();

        return json_encode($this->koalityFormatter->handle($runResult, false), JSON_PRETTY_PRINT);
    }

    private function runOrdersPerTimeIntervalCheck()
    {
        $hours = $this->config[Configuration::ORDERS_PER_TIME_INTERVAL_CHECK][Configuration::HOURS];
        $threshold = $this->config[Configuration::ORDERS_PER_TIME_INTERVAL_CHECK][Configuration::THRESHOLD];
        $this->ordersPerTimeIntervalCheck->init($hours, $threshold);

        $this->healthFoundation->registerCheck(
            $this->ordersPerTimeIntervalCheck,
            'orders_per_time_interval',
            'Shows count of orders during the last ' . $hours . ' hour(s)'
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
