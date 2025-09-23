<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Mailer\Exception\TransportExceptionInterface;
use Symfony\Component\Mailer\Transport;
use Symfony\Component\Mailer\Mailer;
use Symfony\Component\Mime\Email;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    #[Route('/api/order', name: 'api_order_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): JsonResponse
    {
        $data = json_decode($request->getContent(), true);

        // Kötelező mezők ellenőrzése
        $required = ['firstName', 'lastName', 'zipCode', 'city', 'street', 'phone', 'email'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                return new JsonResponse(['error' => "$field is required"], 400);
            }
        }

        // Új rendelés létrehozása
        $order = new Order();
        $order->setFirstName($data['firstName']);
        $order->setLastName($data['lastName']);
        $order->setZipCode($data['zipCode']);
        $order->setCity($data['city']);
        $order->setStreet($data['street']);
        $order->setPhone($data['phone']);
        $order->setEmail($data['email']);
        $order->setCreatedAt(new \DateTime());

        $em->persist($order);
        $em->flush();

        // E-mail küldése
        $dsn = $_ENV['MAILER_DSN'] ?? '';
        $transport = Transport::fromDsn($dsn);
        $mailer = new Mailer($transport);

        // Visszaigazoló e-mail
        $internalEmail = 'nagy.tamas@marso.hu';
        $email = (new Email())
            ->from('kevcode.hu@gmail.com')
            ->to($order->getEmail())
            ->cc($internalEmail)
            ->subject('Rendelés visszaigazolás')
            ->text("Kedves {$order->getFirstName()} {$order->getLastName()},\n\nKöszönjük a rendelésed!\n\nRendelés ID: {$order->getId()}");

        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }

        return new JsonResponse([
            'success' => true,
            'orderId' => $order->getId()
        ]);
    }
}
