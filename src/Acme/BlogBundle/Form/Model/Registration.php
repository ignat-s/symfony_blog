<?php

namespace Acme\BlogBundle\Form\Model;

use Symfony\Component\Validator\Constraints as Assert;
use Acme\BlogBundle\Model\User;

class Registration
{
    /**
     * @Assert\Type(type="Acme\BlogBundle\Model\User")
     * @Assert\Valid()
     * @var User
     */
    private $user;

    /**
     * @Assert\True(message = "registration.terms_accepted.true")
     * @var Boolean
     */
    private $termsAccepted = false;

    public function __construct(User $user)
    {
        $this->user = $user;
    }

    /**
     * @param User $user
     */
    public function setUser(User $user)
    {
        $this->user = $user;
    }

    /**
     * @return User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @return boolean
     */
    public function getTermsAccepted()
    {
        return $this->termsAccepted;
    }

    /**
     * @param boolean $termsAccepted
     */
    public function setTermsAccepted($termsAccepted)
    {
        $this->termsAccepted = (boolean) $termsAccepted;
    }
}