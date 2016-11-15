<?php

namespace FeedbackBundle\Services;

use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\ORM\EntityManager;
use FeedbackBundle\Entity\Comment;
use Monolog\Logger;

// service id: feedback.comment
class CommentService {
    
    /**
     * @var EntityManager
     */
    private $em;
    
    private $logger;
    
    public function setDoctrine(Registry $registry) {
        $this->em = $registry->getManager();
    }
    
    public function setLogger(Logger $logger) {
        $this->logger = $logger;
    }
    
    public function findEntity(Comment $comment) {
        list($class, $id) = explode(':', $comment->getEntity());
        $entity = $this->em->getRepository($class)->find($id);
        return $entity;
    }
    
    public function findComments($entity) {
        $class = get_class($entity);
        $comments = $this->em->getRepository('FeedbackBundle:Comment')->findBy(array(
            'entity' => $class . ':' . $entity->getId()
        ));
        return $comments;
    }
    
    public function addComment($entity, Comment $comment) {
        $comment->setEntity(get_class($entity) . ':' . $entity->getId());
        $this->em->persist($comment);
        $this->em->flush($comment);
        return $comment;
    }
    
}
