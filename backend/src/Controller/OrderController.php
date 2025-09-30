<?php

namespace App\Controller;

use App\Entity\Order;
use App\Entity\Cart;
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

        $session = $request->getSession();
        $sessionId = $session->getId();
        $cart = $em->getRepository(Cart::class)->findOneBy(['sessionId' => $sessionId]);

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
        if ($cart) {
            $order->setCart($cart);
        }
        $em->persist($order);
        $em->flush();

        // E-mail küldése
        $dsn = $_ENV['MAILER_DSN'] ?? '';
        $transport = Transport::fromDsn($dsn);
        $mailer = new Mailer($transport);

        // Visszaigazoló e-mail
        $cartItems = $cart ? $cart->getItems() : [];
        $productRows = '';
        $host = $request->getSchemeAndHttpHost();
        foreach ($cartItems as $item) {
            $product = $item->fetchProduct($em);
            if (!$product) continue;
            $img = $product && $product->getImage() ? $host . $product->getImage() : '';
            $productRows .= "
            <tr>
                <td style='padding:8px;border-bottom:1px solid #eee;'>
                    <img src='$img' alt='' style='height:40px;vertical-align:middle;margin-right:8px;border-radius:6px;border:1px solid #eee;' />
                    <span style='font-weight:bold;'>{$product->getName()}</span>
                </td>
                <td style='padding:8px;border-bottom:1px solid #eee;text-align:center;'>
                    {$item->getQuantity()}
                </td>
            </tr>
            ";
        }

        $html = "
        <h2 style='color:#b91c1c;'>Rendelés visszaigazolás</h2>
        <p>Kedves {$order->getFirstName()} {$order->getLastName()},</p>
        <p>Köszönjük a rendelésed! Az alábbi termékeket választottad:</p>
        <table style='border-collapse:collapse;width:100%;margin-bottom:16px;'>
            <thead>
                <tr>
                    <th style='text-align:left;padding:8px;border-bottom:2px solid #b91c1c;'>Termék</th>
                    <th style='text-align:center;padding:8px;border-bottom:2px solid #b91c1c;'>Mennyiség</th>
                </tr>
            </thead>
            <tbody>
            $productRows
            </tbody>
        </table>
        <p><b>Rendelés azonosító:</b> {$order->getId()}</p>
        <p><b>Szállítási adatok:</b><br>
        {$order->getZipCode()} {$order->getCity()}, {$order->getStreet()}<br>
        Telefon: {$order->getPhone()}<br>
        Email: {$order->getEmail()}</p>
        <p style='color:#b91c1c;'>A rendelésed feldolgozás alatt van, hamarosan keresni fogunk a megadott elérhetőségen.</p>
        <hr>
        <small>Ez egy automatikus visszaigazoló e-mail, kérdés esetén válaszolj erre az üzenetre.</small>
        ";

        $email = (new Email())
            ->from('kevcode.hu@gmail.com')
            ->to($order->getEmail())
            ->cc('kevcode.hu@gmail.com')
            ->subject('Rendelés visszaigazolás')
            ->html($html);
        try {
            $mailer->send($email);
        } catch (TransportExceptionInterface $e) {
            return new JsonResponse([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }

        // Kosár tartalmának törlése (rendelést követően)
        if ($cart) {
            foreach ($cart->getItems() as $item) {
                $em->remove($item);
            }
            $em->flush();
        }

        return new JsonResponse([
            'success' => true,
            'orderId' => $order->getId()
        ]);
    }
}
