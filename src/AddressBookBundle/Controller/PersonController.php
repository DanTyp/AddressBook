<?php

namespace AddressBookBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Component\HttpFoundation\Request;
use AddressBookBundle\Entity\Person;
use AddressBookBundle\Entity\Address;
use AddressBookBundle\Entity\Phone;
use AddressBookBundle\Entity\Email;

class PersonController extends Controller {

    public function generatePersonForm($person, $action) {
        $form = $this->createFormBuilder($person)
                ->setAction($action)
                ->add('name', 'text')
                ->add('surname', 'text')
                ->add('description', 'text')
                ->add('save', 'submit')
                ->getForm();
        return $form;
    }

    public function generateAddressForm($address, $action) {
        $form = $this->createFormBuilder($address)
                ->setAction($action)
                ->add('city', 'text')
                ->add('street', 'text')
                ->add('houseNo', 'text')
                ->add('flatNo', 'text')
                ->add('save', 'submit')
                ->getForm();
        return $form;
    }

    public function generatePhoneForm($phone, $action) {
        $form = $this->createFormBuilder($phone)
                ->setAction($action)
                ->add('number', 'text')
                ->add('type', 'text')
                ->add('save', 'submit')
                ->getForm();
        return $form;
    }

    public function generateEmailForm($email, $action) {
        $form = $this->createFormBuilder($email)
                ->setAction($action)
                ->add('address', 'text')
                ->add('type', 'text')
                ->add('save', 'submit')
                ->getForm();
        return $form;
    }

    /**
     * @Route("/new")
     */
    public function newPersonAction(Request $request) {

        $person = new Person();

        $form = $this->generatePersonForm($person, null);

        $form->handleRequest($request);

        if ($request->getMethod() == 'POST' && $form->isSubmitted()) {
            $person = $form->getData();
            $em = $this->getDoctrine()->getManager();
            $em->persist($person);
            $em->flush();

            return $this->redirectToRoute('addressbook_person_showperson', ['id' => $person->getId()]);
        }

        return $this->render('AddressBookBundle:Person:new.html.twig', ['formPerson' => $form->createView()]);
    }

    /**
     * @Route("/")
     */
    public function showIndexAction() {

        $personsRepo = $this->getDoctrine()->getRepository("AddressBookBundle:Person");
        //$persons = $personsRepo->findAll();
        $persons = $personsRepo->getPersonsOrderedByName();
        return $this->render('AddressBookBundle:Person:show_index.html.twig', ['persons' => $persons]);
    }

    /**
     * @Route("/{id}", requirements={"id"="\d+"})
     */
    public function showPerson($id) {
        $personsRepo = $this->getDoctrine()->getRepository("AddressBookBundle:Person");
        $person = $personsRepo->find($id);

        if ($person == null) {

            throw $this->createNotFoundException();
        }

        return $this->render('AddressBookBundle:Person:show_person.html.twig', ['person' => $person]);
    }

    //poniższa funkcja jest niepotrzebna dodaliśmy ją tylko na potrzeby ćwiczeń
    /**
     * @Route("/add")
     */
    public function addAction() {

        $repo = $this->getDoctrine()->getRepository("AddressBookBundle:Person");
        $em = $this->getDoctrine()->getManager();

        $person = $repo->find(1);

        $address = new Address();
        $address->setCity('Sopot');
        $address->setStreet('Sopocka');
        $address->setHouseNo(rand(10, 100));
        $address->setFlatNo(rand(10, 100));

        //$address->setPerson($person);
        /*
          powyższe moge zakomentować jezeli w pliku Person.php dodam:
          public function addAddress(\AddressBookBundle\Entity\Address $addresses) {
          $addresses->setPerson($this);
          $this->addresses[] = $addresses;

          return $this;
          }
         */

        $person->addAddress($address);

        $em->persist($person);
        $em->flush();

        return $this->redirectToRoute("addressbook_person_showindex");
    }

//        /**
//     * @ORM\OneToMany(targetEntity="Address", mappedBy="person", cascade={"persist"})
//     */
//    private $addresses;
//
//    /**
//     * @ORM\OneToMany(targetEntity="Phone", mappedBy="person", cascade={"persist"}) dodajemy te persisty zeby nam pozwoliło zapisywać dane dotyczące kolumn powiązanych z person, np. jej adres
//     */
//    private $phones;
//
//    /**
//     * @ORM\OneToMany(targetEntity="Email", mappedBy="person", cascade={"persist"})
//     */
//    private $emails;

    /**
     * @Route("/{id}/delete", requirements={"id"="\d+"})
     * @param type $id
     */
    public function deletePersonAction($id) {
        $personsRepo = $this->getDoctrine()->getRepository("AddressBookBundle:Person");
        $personToDelete = $personsRepo->find($id);

        if ($personToDelete != null) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($personToDelete);
            $em->flush();
        }

        return $this->redirectToRoute('addressbook_person_showindex');
    }

    /**
     * @Route("/{id}/deleteAddress", requirements={"id"="\d+"})
     */
    public function deleteAddressAction($id) {
        $addressesRepo = $this->getDoctrine()->getRepository("AddressBookBundle:Address");
        $addressToDelete = $addressesRepo->find($id);

        if ($addressToDelete != null) {
            $person = $addressToDelete->getPerson();

            $em = $this->getDoctrine()->getManager();
            $em->remove($addressToDelete);
            $em->flush();

            return $this->redirectToRoute('addressbook_person_showperson', ['id' => $person->getId()]);
        }
    }

    /**
     * @Route("/{id}/deletePhone", requirements={"id"="\d+"})
     */
    public function deletePhoneAction($id) {
        $phonesRepo = $this->getDoctrine()->getRepository("AddressBookBundle:Phone");
        $phoneToDelete = $phonesRepo->find($id);

        if ($phoneToDelete != null) {
            $person = $phoneToDelete->getPerson();

            $em = $this->getDoctrine()->getManager();
            $em->remove($phoneToDelete);
            $em->flush();

            return $this->redirectToRoute('addressbook_person_showperson', ['id' => $person->getId()]);
        }
    }

    /**
     * @Route("/{id}/deleteEmail", requirements={"id"="\d+"})
     */
    public function deleteEmailAction($id) {
        $emailsRepo = $this->getDoctrine()->getRepository("AddressBookBundle:Email");
        $emailToDelete = $emailsRepo->find($id);

        if ($emailToDelete != null) {
            $person = $emailToDelete->getPerson();

            $em = $this->getDoctrine()->getManager();
            $em->remove($emailToDelete);
            $em->flush();

            return $this->redirectToRoute('addressbook_person_showperson', ['id' => $person->getId()]);
        }
    }

    /**
     * @Route("/{id}/modify", requirements={"id"="\d+"})
     */
    public function modifyPersonAction(Request $request, $id) {

        $personsRepo = $this->getDoctrine()->getRepository("AddressBookBundle:Person");
        $personToModify = $personsRepo->find($id);

        $form = $this->generatePersonForm($personToModify, null);

        $form->handleRequest($request);

        if ($request->getMethod() == 'POST' && $form->isSubmitted()) {
            $personToModify = $form->getData();
            $em = $this->getDoctrine()->getManager();
            //$em->persist($personToModify);
            $em->flush();

            return $this->redirectToRoute('addressbook_person_showperson', ['id' => $id]);
        }

        $address = new Address();
        $action = $this->generateUrl('addressbook_person_addaddress', ['id' => $id]);
        $addressForm = $this->generateAddressForm($address, $action);

        $phone = new Phone();
        $action = $this->generateUrl('addressbook_person_addphone', ['id' => $id]);
        $phoneForm = $this->generatePhoneForm($phone, $action);

        $email = new Email();
        $action = $this->generateUrl('addressbook_person_addemail', ['id' => $id]);
        $emailForm = $this->generateEmailForm($email, $action);

        return $this->render('AddressBookBundle:Person:new.html.twig', [
                    'formPerson' => $form->createView(),
                    'formAddress' => $addressForm->createView(),
                    'formPhone' => $phoneForm->createView(),
                    'formEmail' => $emailForm->createView()
        ]);
    }

    /**
     * @Route("/{id}/addAddress", requirements={"id"="\d+"})
     * @Method("POST")
     */
    public function addAddressAction(Request $request, $id) {

        $address = new Address();

        $form = $this->generateAddressForm($address, null);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $address = $form->getData();
            $personsRepo = $this->getDoctrine()->getRepository("AddressBookBundle:Person");
            $person = $personsRepo->find($id);

            $address->setPerson($person);
            $person->addAddress($address);

            $em = $this->getDoctrine()->getManager();
            $em->persist($address);
            $em->flush();
        }

        return $this->redirectToRoute('addressbook_person_showperson', ['id' => $person->getId()]);
    }

    /**
     * @Route("/{id}/addPhone", requirements={"id"="\d+"})
     * @Method("POST")
     */
    public function addPhoneAction(Request $request, $id) {

        $phone = new Phone();

        $form = $this->generatePhoneForm($phone, null);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $phone = $form->getData();

            $personsRepo = $this->getDoctrine()->getRepository("AddressBookBundle:Person");
            $person = $personsRepo->find($id);

            $phone->setPerson($person);
            $person->addPhone($phone);

            $em = $this->getDoctrine()->getManager();
            $em->persist($phone);
            $em->flush();
        }

        return $this->redirectToRoute('addressbook_person_showperson', ['id' => $person->getId()]);
    }

    /**
     * @Route("/{id}/addEmail", requirements={"id"="\d+"})
     * @Method("POST")
     */
    public function addEmailAction(Request $request, $id) {

        $email = new Email();

        $form = $this->generateEmailForm($email, null);

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            $email = $form->getData();

            $personsRepo = $this->getDoctrine()->getRepository("AddressBookBundle:Person");
            $person = $personsRepo->find($id);

            $email->setPerson($person);
            $person->addEmail($email);

            $em = $this->getDoctrine()->getManager();
            $em->persist($email);
            $em->flush();
        }

        return $this->redirectToRoute('addressbook_person_showperson', ['id' => $person->getId()]);
    }

}
