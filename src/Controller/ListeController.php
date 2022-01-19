<?php

namespace App\Controller;

use App\Entity\Liste;
use App\Entity\ListeElement;
use App\Entity\Vote;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ListeController extends AbstractController
{

    /*
        TODO
        Filter Funktionalität
        Uneingelogte Icon Darstellung verbessern
        Profilfunktionalitäten (Nutzerprofile, Freunde hinzufügen)
        Registrierungsbestätigung/ Passwort vergessen
        Beiträge Melden Funktionalität
        Listen per API generieren (z.B.: Game Releases)
        Auf Domain/ Webspace einrichten
    */

    /**
     * @Route("/lists", name="lists")
     */
    public function lists(ManagerRegistry $doctrine): Response
    {
        $lists = $doctrine->getRepository(Liste::class)->findBy(["creator" => $this->getUser()]);

        return $this->render('liste/lists.html.twig', [
            'controller_name' => 'ListeController',
            'lists' => $lists
        ]);
    }

    /**
     * @Route("/create", name="list_create")
     */
    public function create(Request $request, ManagerRegistry $doctrine): Response
    {
        if ($request->getMethod() === "POST") {

            $manager = $doctrine->getManager();

            $type = $request->get("type");
            $category = $request->get("category");
            $rate = $request->get("rate");
            $name = ltrim(rtrim($request->get("name")));
            $sort = ltrim(rtrim($request->get("sort")));

            $list = new Liste();
            $list->setName($name);
            $list->setType($type);
            $list->setCategory($category);
            $list->setRating($rate);
            $list->setSort($sort);
            $list->setCreator($this->getUser());
            $list->setCreated(new \DateTime());

            if ($request->get("showdate") == "yes") {
                $list->setShowdate(true);
            } else {
                $list->setShowdate(false);
            }

            if ($request->get("showinfo") == "yes") {
                $list->setShowinfo(true);
            } else {
                $list->setShowinfo(false);
            }

            if ($request->get("free") == "free") {
                $list->setPrivate(false);
            } else {
                $list->setPrivate(true);
            }

            $manager->persist($list);
            $manager->flush();

            return $this->redirectToRoute("list_view", ["id" => $list->getId()]);
        }

        return $this->redirectToRoute("front");
    }

    /**
     * @Route("/liste/{id}", name="list_view")
     */
    public function list($id, Request $request, ManagerRegistry $doctrine, Filesystem $filesystem): Response
    {
        $list = $doctrine->getRepository(Liste::class)->findOneBy(["id" => $id]);

        //TODO Calculate Votes → Besser bei großen Datenmengen in Element anlegen und beim Voten hochzählen
        foreach ($list->getElements() as $element) {

            foreach ($element->getVotes() as $vote) {

                if ($element->getListe()->getRating() != "stars") {

                    //LIKE
                    if ($vote->getType() != "stars") {
                        if ($vote->getValue() == +1) {
                            if ($vote->getCreator() === $this->getUser()) {
                                $element->upvoted = true;
                            }
                            if (!property_exists($element, "upvotes")) {
                                $element->upvotes = 1;
                            }
                            $element->upvotes = $element->upvotes++;
                        } else if ($vote->getValue() == -1) {
                            if ($vote->getCreator() === $this->getUser()) {
                                $element->downvoted = true;
                            }
                            if (!property_exists($element, "downvotes")) {
                                $element->downvotes = 1;
                            }
                            $element->downvotes = $element->downvotes++;
                        }
                    }
                } else {

                    //STARS
                    $werte = 0;
                    $votes = $element->getVotes();
                    $count = 0;
                    foreach ($votes as $_vote) {
                        if ($vote->getType() != "like") {
                            $count++;
                            if ($vote->getCreator() == $this->getUser()) {
                                $_vote->getElement()->starrated = $_vote->getValue();
                            }
                            $werte += $_vote->getValue();
                        }
                    }

                    if ($count) {
                        if ($count > 0) {
                            $element->starvotes = $count;
                        } else {
                            $element->starvotes = "";
                        }
                        $element->stars = round((($werte / $count) * 2));
                    }
                }
            }

            if ($element->getListe()->getRating() != "stars") {

                //LIKE
                if (!property_exists($element, "upvotes")) {
                    $element->upvotes = 0;
                }
                if (!property_exists($element, "downvotes")) {
                    $element->downvotes = 0;
                }
            } else {
                if (!property_exists($element, "stars")) {
                    $element->stars = 0;

                }
                if (!property_exists($element, "votes")) {
                    $element->starvotes = "";
                }

            }
        }


        if ($request->getMethod() == "POST") {

            if ($this->getUser()) {

                $manager = $doctrine->getManager();

                if ($request->get("copy_id")) {

                    //ELEMENT KOPIEREN
                    $copy_elememt = $doctrine->getRepository(ListeElement::class)->find($request->get("copy_id"));

                    if ($copy_elememt) {

                        if ($request->get("newlist") == "yes") {

                            $type = $request->get("type");
                            $category = $request->get("category");
                            $rate = $request->get("rate");
                            $name = ltrim(rtrim($request->get("newlistname")));
                            $sort = ltrim(rtrim($request->get("sort")));

                            $new_list = new Liste();
                            $new_list->setName($name);
                            $new_list->setType($type);
                            $new_list->setCategory($category);
                            $new_list->setRating($rate);
                            $new_list->setSort($sort);
                            $new_list->setCreator($this->getUser());
                            $new_list->setCreated(new \DateTime());

                            if ($request->get("showdate") == "yes") {
                                $new_list->setShowdate(true);
                            } else {
                                $new_list->setShowdate(false);
                            }

                            if ($request->get("showinfo") == "yes") {
                                $new_list->setShowinfo(true);
                            } else {
                                $new_list->setShowinfo(false);
                            }

                            if ($request->get("free") == "free") {
                                $new_list->setPrivate(false);
                            } else {
                                $new_list->setPrivate(true);
                            }

                            $manager->persist($new_list);

                            $cloned_element = clone $copy_elememt;
                            $manager->persist($cloned_element);
                            $new_list->addElement($cloned_element);

                            $manager->flush();

                            return $this->redirectToRoute("list_view", ["id" => $new_list->getId()]);

                        } else {

                            $copy_list = $doctrine->getRepository(Liste::class)->find($request->get("list"));

                            if ($copy_list) {
                                $cloned_element = clone $copy_elememt;
                                $manager->persist($cloned_element);
                                $copy_list->addElement($cloned_element);
                                $manager->flush();
                                return $this->redirectToRoute("list_view", ["id" => $copy_list->getId()]);
                            }

                        }

                    }

                } else if ($request->get("list_id") && $list->getCreator() == $this->getUser()) {

                    //LISTE BEARBEITEN
                    $type = $request->get("type");
                    $category = $request->get("category");
                    $rate = $request->get("rate");
                    $name = ltrim(rtrim($request->get("name")));
                    $sort = ltrim(rtrim($request->get("sort")));

                    $list->setName($name);
                    $list->setType($type);
                    $list->setCategory($category);
                    $list->setRating($rate);
                    $list->setSort($sort);

                    $list->setCreated(new \DateTime());

                    if ($request->get("free") == "free") {
                        $list->setPrivate(false);
                    } else {
                        $list->setPrivate(true);
                    }

                    if ($request->get("showdate") == "yes") {
                        $list->setShowdate(true);
                    } else {
                        $list->setShowdate(false);
                    }

                    if ($request->get("showinfo") == "yes") {
                        $list->setShowinfo(true);
                    } else {
                        $list->setShowinfo(false);
                    }

                    $manager->flush();

                } else if ($request->get("edit_id")) {

                    //ELEMENT EDITIEREN
                    $name = $request->get("name");
                    $element_id = $request->get("edit_id");
                    $date = $request->get("date");
                    $info = $request->get("info");

                    if ($id) {

                        $element = $doctrine->getRepository(ListeElement::class)->find($element_id);

                        $element->setName($name);

                        if ($date) {
                            $element->setDate(\DateTime::createFromFormat('Y-m-j', $date));
                        } else {
                            $element->setDate(null);
                        }


                        $element->setInfo($info);


                        $file = $request->files->get("img");

                        if ($file) {
                            $filesystem->mkdir("files/" . $this->getUser()->getId());
                            $path = "files/" . $this->getUser()->getId() . "/" . $element->getId() . ".png";
                            $filesystem->copy($file->getPathname(), $path);
                            $element->setImg("../" . $path);

                        }

                        $manager->flush();

                    }

                } else {

                    //ELEMENT HINZUFÜGEN
                    $name = $request->get("name");
                    $coverurl = $request->get("coverurl");
                    $date = $request->get("date");
                    $info = $request->get("info");

                    $listeElement = new ListeElement();
                    $listeElement->setName($name);
                    $listeElement->setListe($list);

                    if ($info) {
                        $listeElement->setInfo($info);
                    }

                    if ($date) {
                        $listeElement->setDate(\DateTime::createFromFormat('Y-m-j', $date));
                    }

                    $listeElement->setCreator($this->getUser());

                    if ($coverurl != "") {
                        $listeElement->setImg($coverurl);
                    }

                    $manager->persist($listeElement);
                    $manager->flush();

                    $file = $request->files->get("img");

                    if ($file) {
                        $filesystem->mkdir("files/" . $this->getUser()->getId());
                        $path = "files/" . $this->getUser()->getId() . "/" . $listeElement->getId() . ".png";
                        $filesystem->copy($file->getPathname(), $path);
                        $listeElement->setImg("../" . $path);
                        $manager->flush();
                    }
                }

                return $this->redirectToRoute("list_view", ["id" => $id]);

            } else {

                return $this->redirectToRoute("login");

            }

        }

        return $this->render('liste/view.html.twig', [
            'controller_name' => 'ListeController',
            'list' => $list,
            'elements' => $list->getElements()
        ]);
    }

    /**
     * @Route("/deletelist/{id}", name="ajax_deletelist")
     */
    public function ajax_deletelist($id, Request $request, ManagerRegistry $doctrine): Response
    {
        $list = $doctrine->getRepository(Liste::class)->find($id);
        if ($list && $this->getUser() == $list->getCreator()) {
            $doctrine->getManager()->remove($list);
            $doctrine->getManager()->flush();
        }
        return $this->redirectToRoute("lists");
    }

    /**
     * @Route("/ajax/delete", name="ajax_delete")
     */
    public function ajax_delete(Request $request, ManagerRegistry $doctrine): Response
    {
        if ($request->getMethod() == "POST") {
            $element_id = $request->get("id");
            if ($element_id) {
                $element = $doctrine->getRepository(ListeElement::class)->find($element_id);
                if ($element && $element->getCreator() == $this->getUser() or $element->getListe()->getCreator() == $this->getUser()) {
                    $doctrine->getManager()->remove($element);
                    $doctrine->getManager()->flush();
                    return new JsonResponse("true");
                }
            }
        }
        return new JsonResponse();
    }

    /**
     * @Route("/ajax/modifydate", name="ajax_modifydate")
     */
    public function ajax_modifydate(Request $request, ManagerRegistry $doctrine): Response
    {
        if ($request->getMethod() == "POST") {
            $element_id = $request->get("id");
            $date = $request->get("date");
            if ($element_id && $date) {
                $element = $doctrine->getRepository(ListeElement::class)->find($element_id);
                if ($element && $element->getCreator() == $this->getUser() or $element->getListe()->getCreator() == $this->getUser()) {
                    $element->setDate(\DateTime::createFromFormat('Y-m-j', $date));
                    $doctrine->getManager()->flush();
                }
            }
        }
        return new Response();
    }


    /**
     * @Route("/ajax/element/vote", name="ajax_element_vote")
     */
    public function ajax_element_vote(Request $request, ManagerRegistry $doctrine): Response
    {
        if ($request->getMethod() == "POST") {

            if ($this->getUser()) {

                $element_id = $request->get("id");
                $value = $request->get("value");

                //if (($value == -1 || $value == 1) && $element_id) {
                if ($value && $element_id) {

                    $manager = $doctrine->getManager();

                    $element = $doctrine->getRepository(ListeElement::class)->find($element_id);

                    $rating = "";
                    if ($element->getListe()->getRating() == "stars") {
                        $rating = "stars";
                    } else {
                        $rating = "like";
                    }

                    $voted_befor = $doctrine->getRepository(Vote::class)->findOneBy(["element" => $element, "creator" => $this->getUser(), "type" => $rating]);

                    if ($element->getListe()->getRating() == "stars") {

                        if ($voted_befor) {
                            if ($voted_befor->getValue() == $value) {
                                $manager->remove($voted_befor);
                                $manager->flush();
                                return new JsonResponse(false);
                            }
                        }

                        //STARS
                        if (!$voted_befor) {
                            $vote = new Vote();
                            $vote->setCreator($this->getUser());
                            $vote->setElement($element);
                            $vote->setType("stars");
                            $manager->persist($vote);
                        } else {
                            $vote = $voted_befor;
                        }
                        $vote->setValue($value); //1-6

                        $manager->flush();

                        $count = 0;
                        foreach ($element->getVotes() as $_vote) {
                            if ($_vote->getType() == "stars") {
                                $count++;
                            }
                        }

                        return new JsonResponse(array($count, $value * 2));

                    } else {

                        //LIKE
                        if ($voted_befor) {
                            $manager->remove($voted_befor);
                        }

                        $vote = new Vote();
                        $vote->setCreator($this->getUser());
                        $vote->setElement($element);
                        $vote->setValue($value);
                        $vote->setType("like");

                        $manager->persist($vote);
                        $manager->flush();

                        $upvotes = $doctrine->getRepository(Vote::class)->findBy(["element" => $element, "value" => 1]);
                        $downvotes = $doctrine->getRepository(Vote::class)->findBy(["element" => $element, "value" => -1]);

                        return new JsonResponse(array(count($upvotes), count($downvotes)));

                    }

                }

            }
        }
        return new JsonResponse(false);
    }


    /**
     * @Route("/ajax/favorite/add", name="ajax_favorite_add")
     */
    public function ajax_favorite_add(Request $request, ManagerRegistry $doctrine): Response
    {
        if ($request->getMethod() == "POST") {
            $id = $request->get("id");
            if ($id) {
                $list = $doctrine->getRepository(Liste::class)->find($id);
                $this->getUser()->addFavorit($list);
                $doctrine->getManager()->flush();
                if ($list->getUsers() && count($list->getUsers()) > 0) {
                    return new JsonResponse(count($list->getUsers()));
                }

            }
        }
        return new JsonResponse("");
    }

    /**
     * @Route("/ajax/favorite/remove", name="ajax_favorite_remove")
     */
    public function ajax_favorite_remove(Request $request, ManagerRegistry $doctrine): Response
    {
        if ($request->getMethod() == "POST") {
            $id = $request->get("id");
            if ($id) {
                $list = $doctrine->getRepository(Liste::class)->find($id);
                $this->getUser()->removeFavorit($list);
                $doctrine->getManager()->flush();
                if ($list->getUsers() && count($list->getUsers()) > 0) {
                    return new JsonResponse(count($list->getUsers()));
                }
            }
        }
        return new JsonResponse();
    }

    /**
     * @Route("/ajax/element/removevote", name="ajax_element_removevote")
     */
    public function ajax_element_removevote(Request $request, ManagerRegistry $doctrine): Response
    {
        if ($request->getMethod() == "POST") {

            if ($this->getUser()) {

                $element_id = $request->get("id");
                $type = $request->get("type");


                $manager = $doctrine->getManager();

                $element = $doctrine->getRepository(ListeElement::class)->find($element_id);

                $voted_befor = $doctrine->getRepository(Vote::class)->findOneBy(["element" => $element, "creator" => $this->getUser(), "type" => $type]);

                if ($voted_befor) {
                    $manager->remove($voted_befor);
                    $manager->flush();
                }

                if ($element->getListe()->getRating() == "norating") {
                    return new JsonResponse();
                }

                if ($element->getListe()->getRating() == "stars") {
                    //STARS
                    $werte = 0;
                    $votes = $element->getVotes();
                    foreach ($votes as $_vote) {
                        $werte += $_vote->getValue();
                    }

                    if (count($votes)) {

                        $count = 0;
                        foreach ($element->getVotes() as $_vote) {
                            if ($_vote->getType() == "stars") {
                                $count++;
                            }
                        }

                        return new JsonResponse(array($count, round((($werte / count($votes)) * 2))));
                    } else {
                        return new JsonResponse(array(0, 0));
                    }

                }

                $upvotes = $doctrine->getRepository(Vote::class)->findBy(["element" => $element, "value" => 1]);
                $downvotes = $doctrine->getRepository(Vote::class)->findBy(["element" => $element, "value" => -1]);

                return new JsonResponse(array(count($upvotes), count($downvotes)));

            }
        }
        return new JsonResponse(false);
    }

}

