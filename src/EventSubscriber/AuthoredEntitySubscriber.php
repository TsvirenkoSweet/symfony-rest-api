<?php
/**
 * Created by PhpStorm.
 * User: andriit
 * Date: 3/15/2019
 * Time: 3:55 PM
 */

namespace App\EventSubscriber;


use ApiPlatform\Core\EventListener\EventPriorities;
use App\Entity\AuthoredEntityInterface;
use App\Entity\Comment;
use App\Entity\Product;
use PhpParser\Node\Expr\Instanceof_;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\GetResponseForControllerResultEvent;
use Symfony\Component\HttpKernel\KernelEvents;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class AuthoredEntitySubscriber implements EventSubscriberInterface
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    public function __construct(TokenStorageInterface $tokenStorage)
    {

        $this->tokenStorage = $tokenStorage;
    }

    public static function getSubscribedEvents()
    {
        return[
          KernelEvents::VIEW => ['getAutenticatedUser', EventPriorities::PRE_WRITE]
        ];
    }
    public function getAutenticatedUser(GetResponseForControllerResultEvent $event)
    {
        $entity = $event->getControllerResult();
        $method = $event->getRequest()->getMethod();

        $author =  $this->tokenStorage->getToken()->getUser();


        if(!$entity instanceof  AuthoredEntityInterface || Request::METHOD_POST !== $method){
            return;
        }

        $entity->setAuthor($author);
    }
}