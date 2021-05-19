<?php


namespace Webapp\Serializer\User;


use Symfony\Component\Serializer\Normalizer\NormalizerInterface;
use Webapp\Entity\User;

class UserSerializer implements NormalizerInterface
{
    /**
     * @inheritDoc
     *
     * @var User $object
     */
    public function normalize($object, $format = null, array $context = [])
    {
        switch ($context['collection_operation_name']) {
            case 'register':
                $content = [
                    'data' => [
                        'json' => [
                            'type' => 'user',
                            'attributes' => [
                                'firstname' => $object->getFirstName(),
                                'lastname' => $object->getLastName(),
                                'email' => $object->getEmail()
                            ]
                        ]
                    ]
                ];

                $resp = json_encode($content, JSON_THROW_ON_ERROR, 512);
                break;
            case 'login':
                $resp = [
                    'access_token' => $object->getToken()
                ];
                break;
        }

        return $resp;
    }

    /**
     * @inheritDoc
     */
    public function supportsNormalization($data, $format = null)
    {
        return is_a($data, User::class) ?: false;
    }
}
