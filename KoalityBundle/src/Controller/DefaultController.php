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

class DefaultController extends FrontendController
{
    private HealthFoundation $healthFoundation;
    private KoalityFormat  $koalityFormatter;

    private SpaceUsedCheck $spaceUsedCheck;
    private ContainerIsRunningCheck $containerIsRunningCheck;

    private OrdersPerHourCheck $ordersPerHourCheck;


    /**
     * @Route("/koality-status")
     */
    public function indexAction()
    {
        $this->init();

        //$this->getNewestOrders();exit;

        $response = new Response();
        $response->setContent($this->runChecks());
        $response->headers->set('Content-Type', 'application/health+json');
        $response->send();



        return $response;
    }

    private function init(){
        $this->healthFoundation = new HealthFoundation();
        $this->koalityFormatter = new KoalityFormat();

        $this->spaceUsedCheck = new SpaceUsedCheck();
        $this->containerIsRunningCheck = new ContainerIsRunningCheck();

        $this->ordersPerHourCheck = new OrdersPerHourCheck();
    }

    /**
     *
     */
    public function runChecks() {

        $this->runSpaceUsedCheck();
        $this->runContainerIsRunningCheck();
        $this->runOrdersPerHourCheck();



        $runResult = $this->healthFoundation->runHealthCheck();

        /**Necessary, because the handle function is only echoing the JSON Response*/
        ob_start();
        $this->koalityFormatter->handle($runResult);
        $ietfJson = ob_get_contents();
        ob_clean();

        return $ietfJson;
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

    private function runOrdersPerHourCheck() {

        $this->healthFoundation->registerCheck(
            $this->ordersPerHourCheck,
            'orders_per_hour_check',
            'Shows count of orders during the last hour'
        );
    }
}
