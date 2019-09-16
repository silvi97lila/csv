<?php

namespace App\Controller;

use App\Entity\Contact;
use App\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Finder\Finder;
use FOS\UserBundle\Doctrine\UserManager;


class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index()
    {
        $em=$this->getDoctrine()->getManager();

        $data = [];

        $num=0;

        $userManager = $container->get('fos_user.user_manager');

        dump($userManager);exit;

        if (($fp = fopen($csv, "r")) !== FALSE) {
            while (($row = fgetcsv($fp, 1000, ",")) !== FALSE) {
                $data[$num] = $row;
                $num++;
            }
            fclose($fp);
        }
        unset($data[0]);

            foreach ($data as $item) {
                    $contact=new Contact();
                    $contact->setCompanyName($item[2]);
                    $contact->setAddress($item[3]);
                    $contact->setCity($item[4]);
                    $contact->setCountry($item[5]);
                    $contact->setState($item[6]);
                    $contact->setZip($item[7]);
                    $contact->setPhone($item[8]);
                    $contact->setPhone1($item[9]);


                    $user = $userManager->createUser();
                    $user->setName($item[0]);
                    $user->setLastName($item[1]);
                    $user->setUsername($item[10]);
                    $user->setEmail($item[10]);
                    $user->setPassword('$2y$13$Kp1nFnfae05QdT0QkZhLYe0GcQDWS2knWNnJC0flVy6ky0ObsA0AK');
                    $user->setEnabled(true);
                    $user->addContact($contact);

                    $em->persist($user);
                    $em->persist($contact);

            }

            $em->flush();

        return new Response("The data was inserted successfully into the database. View your profile");
    }

    /**
     * @Route("/profile", name="profile")
     */
    public function profile(){

        $user = $this->container->get('security.token_storage')->getToken()->getUser();
        $loginId=$user->getId();

        $data=$this->getDoctrine()->getRepository(User::class)->find($loginId);

        $contact=$data->getContact()->getValues();
        return $this->render('/home/profile.html.twig', ['data'=>$data,'contact'=>$contact]);
    }
}
