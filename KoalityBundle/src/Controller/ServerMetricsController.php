<?php

namespace Basilicom\KoalityBundle\Controller;

use Basilicom\KoalityBundle\DependencyInjection\Configuration;
use Leankoala\HealthFoundation\Check\Device\SpaceUsedCheck;
use Leankoala\HealthFoundation\Check\Docker\Container\ContainerIsRunningCheck;
use Leankoala\HealthFoundation\Check\System\UptimeCheck;
use Leankoala\HealthFoundation\HealthFoundation as HealthFoundation;
use Leankoala\HealthFoundation\Result\Format\Koality\KoalityFormat as KoalityFormat;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ServerMetricsController extends FrontendController
{
    private HealthFoundation $healthFoundation;
    private KoalityFormat  $koalityFormatter;

    private SpaceUsedCheck $spaceUsedCheck;
    private ContainerIsRunningCheck $containerIsRunningCheck;
    private UptimeCheck $uptimeCheck;

    private array $config;

    public function __construct($config)
    {
        $this->config = $config;
        $this->init();
    }

    /**
     * @Route("/pimcore-koality-bundle-server")
     */
    public function serverChecksAction()
    {
        $response = new Response();
        $response->setContent($this->runServerChecks());
        $response->headers->set('Content-Type', 'application/health+json');
        $response->send();

        return $response;
    }

    private function runServerChecks()
    {
        if ($this->config[Configuration::SPACE_USED_CHECK][Configuration::ENABLE] === true) {
            $this->runSpaceUsedCheck();
        }
        if ($this->config[Configuration::SERVER_UPTIME_CHECK][Configuration::ENABLE] === true) {
            $this->runServerUptimeCheck();
        }
        if ($this->config[Configuration::CONTAINER_IS_RUNNING_CHECK][Configuration::ENABLE] === true) {
            $this->runContainerIsRunningCheck();
        }

        $runResult = $this->healthFoundation->runHealthCheck();

        return json_encode($this->koalityFormatter->handle($runResult, false), JSON_PRETTY_PRINT);
    }

    private function runSpaceUsedCheck()
    {
        $limitInPercent = $this->config[Configuration::SPACE_USED_CHECK][Configuration::LIMIT_IN_PERCENT];
        $this->spaceUsedCheck->init($limitInPercent);

        $this->healthFoundation->registerCheck(
            $this->spaceUsedCheck,
            'space_used_check',
            'Space used on storage server. Limit is set to ' . $limitInPercent . ' percent'
        );
    }

    private function runContainerIsRunningCheck()
    {
        $containerName = $this->config[Configuration::CONTAINER_IS_RUNNING_CHECK][Configuration::CONTAINER_NAME];
        $this->containerIsRunningCheck->init($containerName);

        $this->healthFoundation->registerCheck(
            $this->containerIsRunningCheck,
            'container_is_running_check',
            'Is the Docker Container - ' . $containerName . ' - still running?'
        );
    }

    private function runServerUptimeCheck()
    {
        $serverUptimeLimit = $this->config[Configuration::SERVER_UPTIME_CHECK][Configuration::TIME_INTERVAL];
        $this->uptimeCheck->init($serverUptimeLimit);

        $this->healthFoundation->registerCheck(
            $this->uptimeCheck,
            'server_uptime_check',
            'Shows the server uptime. Gives warning, if Limit of ' . $serverUptimeLimit . ' is exceeded.'
        );
    }

    private function init()
    {
        $this->healthFoundation = new HealthFoundation();
        $this->koalityFormatter = new KoalityFormat();

        //HealthFoundationChecks
        $this->spaceUsedCheck = new SpaceUsedCheck();
        $this->containerIsRunningCheck = new ContainerIsRunningCheck();
        $this->uptimeCheck = new UptimeCheck();
    }
}
