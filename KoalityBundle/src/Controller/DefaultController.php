<?php

namespace Basilicom\KoalityBundle\Controller;

use Leankoala\HealthFoundation\Result\Format\Ietf\IetfFormat as IetfFormat;
use Pimcore\Controller\FrontendController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Leankoala\HealthFoundation\HealthFoundation as HealthFoundation;
use Leankoala\HealthFoundation\Check\Device\SpaceUsedCheck;
use Leankoala\HealthFoundation\Check\Docker\Container\ContainerIsRunningCheck;


class DefaultController extends FrontendController
{
    private HealthFoundation $healthFoundation;
    private SpaceUsedCheck $spaceUsedCheck;
    private ContainerIsRunningCheck $containerIsRunningCheck;
    private IetfFormat $formatter;
    private array $checks;


    /**
     * @Route("/koality-status")
     */
    public function indexAction(Request $request )
    {
        $this->init();
        $this->runChecks();

        return new Response('hallo welt');
    }

    /*
     * Einfach alle Checks mit registerCheck registrieren und dann runHealthCheck ausführen
     * So kann ich auch eigene Checks hinzufügen....muss mich aber an die Struktur vom Formater etc halten.
     *
     * */
    public function runChecks() {

        $this->runSpaceUsedCheck();
        $this->runContainerIsRunningCheck();



        $runResult = $this->healthFoundation->runHealthCheck();


        //$this->formatter->handle($runResult);
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


    private function init(){
        $this->formatter = new IetfFormat();
        $this->spaceUsedCheck = new SpaceUsedCheck();
        $this->healthFoundation = new HealthFoundation();
        $this->containerIsRunningCheck = new ContainerIsRunningCheck();
    }
}
