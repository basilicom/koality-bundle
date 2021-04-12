<?php

namespace Basilicom\KoalityBundle\Controller;

use Basilicom\KoalityBundle\Checks\OrdersPerHourCheck;
use Leankoala\HealthFoundation\Result\Format\Koality\KoalityFormat as KoalityFormat;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Leankoala\HealthFoundation\HealthFoundation as HealthFoundation;
use Leankoala\HealthFoundation\Check\Device\SpaceUsedCheck;
use Leankoala\HealthFoundation\Check\Docker\Container\ContainerIsRunningCheck;
use Leankoala\HealthFoundation\Check\System\UptimeCheck;

class DefaultController extends FrontendController
{
    private HealthFoundation $healthFoundation;
    private KoalityFormat  $koalityFormatter;

    private SpaceUsedCheck $spaceUsedCheck;
    private ContainerIsRunningCheck $containerIsRunningCheck;
    private UptimeCheck $uptimeCheck;

    private OrdersPerHourCheck $ordersPerHourCheck;

    /**
     * @Route("/koality-status")
     */
    public function indexAction()
    {
        $this->init();

        $response = new Response();
        $response->setContent($this->runChecks());
        $response->headers->set('Content-Type', 'application/health+json');
        $response->send();

        return $response;
    }

    public function runChecks() {

        $this->runSpaceUsedCheck();
        $this->runOrdersPerHourCheck();
        $this->runServerUptimeCheck();

        $runResult = $this->healthFoundation->runHealthCheck();

        return json_encode($this->koalityFormatter->handle($runResult, false), JSON_PRETTY_PRINT);
    }

    private function runSpaceUsedCheck() {
        $this->spaceUsedCheck->init(95);

        $this->healthFoundation->registerCheck(
            $this->spaceUsedCheck,
            'space_used_check',
            'Space used on storage server'
        );
    }

    private function runContainerIsRunningCheck() {
        $this->containerIsRunningCheck->init('koality_bundle_skeleton_php-fpm_1');

        $this->healthFoundation->registerCheck(
            $this->containerIsRunningCheck,
            'container_is_running_check',
            'Is the Docker Container still running?'
        );
    }

    private function runServerUptimeCheck() {
        $this->uptimeCheck->init('2 years');

        $this->healthFoundation->registerCheck(
            $this->uptimeCheck,
            'server_uptime_check',
            'Shows the server uptime. Gives warning, if Limit is exceeded.'
        );
    }

    private function runOrdersPerHourCheck() {

        $this->healthFoundation->registerCheck(
            $this->ordersPerHourCheck,
            'orders_per_hour_check',
            'Shows count of orders during the last hour'
        );
    }

    private function init(){
        $this->healthFoundation = new HealthFoundation();
        $this->koalityFormatter = new KoalityFormat();

        //HealthFoundationChecks
        $this->spaceUsedCheck = new SpaceUsedCheck();
        $this->containerIsRunningCheck = new ContainerIsRunningCheck();
        $this->uptimeCheck = new UptimeCheck();
        //Pimcore eCommerce Framework Checks

        $this->ordersPerHourCheck = new OrdersPerHourCheck();
    }
}
