<?php

declare(strict_types=1);

/*
 * This file is part of the SolidPlan project.
 *
 * @author     pierre
 * @copyright  Copyright (c) 2019
 */

namespace App\Serializer;

use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Symfony\Component\Serializer\SerializerAwareInterface;
use Symfony\Component\Serializer\SerializerInterface;

class UserPasswordNormalizer implements NormalizerInterface, DenormalizerInterface, SerializerAwareInterface
{
  /**
   * @var NormalizerInterface
   */
  private $normalizer;

  /**
   * @var UserPasswordEncoderInterface
   */
  private $passwordEncoder;

  public function __construct(NormalizerInterface $normalizer, UserPasswordEncoderInterface $passwordEncoder)
  {
    if (!$normalizer instanceof DenormalizerInterface) {
      throw new \InvalidArgumentException(sprintf('The decorated normalizer must implement the %s.', DenormalizerInterface::class));
    }

    $this->normalizer = $normalizer;
    $this->passwordEncoder = $passwordEncoder;
  }

  /**
   * {@inheritDoc}
   */
  public function denormalize($data, $class, $format = null, array $context = [])
  {
    $data = $this->normalizer->denormalize($data, $class, $format, $context);

    if ($data instanceof User) {
      $data->setPassword($this->passwordEncoder->encodePassword($data, $data->getPassword()));
    }

    return $data;
  }

  /**
   * {@inheritDoc}
   */
  public function supportsDenormalization($data, $type, $format = null): bool
  {
    return User::class === $type && $this->normalizer->supportsDenormalization($data, $type, $format);
  }

  /**
   * {@inheritDoc}
   */
  public function normalize($object, $format = null, array $context = [])
  {
    return $this->normalizer->normalize($object, $format, $context);
  }

  /**
   * {@inheritDoc}}
   */
  public function supportsNormalization($data, $format = null): bool
  {
    return $this->normalizer->supportsNormalization($data, $format);
  }

  /**
   * {@inheritDoc}
   */
  public function setSerializer(SerializerInterface $serializer)
  {
    if($this->normalizer instanceof SerializerAwareInterface) {
      $this->normalizer->setSerializer($serializer);
    }
  }
}