<?php

namespace App\Events;

use App\Kernel;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\KernelEvent;
use Symfony\Component\EventDispatcher\Attribute\AsEventListener;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 *
 */
class KernelEventListener {

    /**
     * @param ManagerRegistry $doctrine
     * @param KernelInterface $kernel
     */
    public function __construct(
        private ManagerRegistry $doctrine,
        private KernelInterface $kernel
    ) {
    }

    /**
     * @param KernelEvent $event
     * @return void
     * @throws \Exception
     */
    #[AsEventListener(event: KernelEvents::REQUEST)]
    public function onRequest(KernelEvent $event): void {
        $dbName = $this->getDatabaseNameFromOrigin($event->getRequest());
        if (in_array($dbName, $this->doctrine->getConnection()->getDatabases())) {
            $this->doctrine->getConnection()->changeDatabase($dbName);
        } else {
            if ($this->kernel->getEnvironment() === 'prod') {
                throw new \Exception('No database available for given origin');
            }
        }

    }

    /**
     * @param Request $request
     * @return string
     */
    private function getDatabaseNameFromOrigin(Request $request): string {
        $origin = $request->headers->get('origin');
        $exploded = explode('.', $origin);
        return 'app_' . $exploded[0];
    }
}
