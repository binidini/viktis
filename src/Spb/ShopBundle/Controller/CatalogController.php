<?php

namespace Spb\ShopBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Finder\Finder;

class CatalogController extends Controller {

    /**
     * @Route("/catalog/rootnodes", name="spb_shop_catalog_rootnodes")
     */
    public function rootNodesAction() {

        $em = $this->getDoctrine()->getManager();

        $repo = $em->getRepository('SpbShopBundle:Category');

        $controller = $this;

        $options = array(
            'decorate' => true,
            'rootOpen' => '',
            'rootClose' => '',
            'childOpen' => '<li>',
            'childClose' => '</li>',
            'nodeDecorator' => function($node) use (&$controller) {
                return '<a href="' . $controller->generateUrl('spb_shop_catalog_category', array('slug'=>$node['slug'], 'tag'=>$node['tag'])) . '"><i class="icon-chevron-right"></i>' . $node['title'] . '</a>';
            }
        );

        $htmlTree = $repo->childrenHierarchy(
                null, /* starting from root nodes */
                true, /* load only direct */
                $options
        );

        $response = new Response($htmlTree);
        // пометить ответ как public или private
        $response->setPublic();
        //$response->setPrivate();

        // установить max age для private и shared ответов
        $response->setMaxAge(6000);
        $response->setSharedMaxAge(6000);

        // установить специальную директиву Cache-Control
        $response->headers->addCacheControlDirective('must-revalidate', true);

        return $response;
    }

    /**
     * @Route("/catalog/fulltree", name="spb_shop_catalog_fulltree")
     */
    public function fullTreeAction() {

        $em = $this->getDoctrine()->getManager();

        $repo = $em->getRepository('SpbShopBundle:Category');

        $controller = $this;

        $options = array(
            'decorate' => true,
            'rootOpen' => '<ul class="dropdown-menu">',
            'rootClose' => '</ul>',
            'childOpen' => function($child) {
                if ( ( $child['rgt'] - $child['lft'] ) == 1 ){
                    return '<li>';
                } else {
                    return '<li class="dropdown-submenu">';
                }
              },
            'childClose' => '</li>',
            'nodeDecorator' => function($node) use (&$controller) {
                return '<a href="' . $controller->generateUrl('spb_shop_catalog_category', array('slug'=>$node['slug'], 'tag'=>$node['tag'])) . '">' . $node['title'] . '</a>';
            }
        );

        $htmlTree = $repo->childrenHierarchy(
                null, /* starting from root nodes */
                false, /* load all children, not only direct */
                $options
        );

        $response = new Response($htmlTree);

        // пометить ответ как public или private
        $response->setPublic();
        //$response->setPrivate();
        //
        // установить max age для private и shared ответов
        $response->setMaxAge(6000);
        $response->setSharedMaxAge(6000);

        // установить специальную директиву Cache-Control
        $response->headers->addCacheControlDirective('must-revalidate', true);

        return $response;
    }

    /**
     * @Route("/catalog/{slug}/{tag}/", name="spb_shop_catalog_category")
     */
    public function categoryAction($slug) {

        $em = $this->getDoctrine()->getManager();

        $repo = $em->getRepository('SpbShopBundle:Category');

        $entity = $repo->findOneBySlug($slug);
        $parents = $repo->getPath($entity);

        if ( $repo->childCount($entity) > 0 ) {

            $children = $repo->children($entity, true/*direct*/);

            return $this->render(
                'SpbShopBundle:Catalog:category.html.twig',
                array('entity' => $entity, 'parents'=>$parents,'items' => $children)
            );
        } else {

            $finder = new Finder();
            $finder->files()->in($this->container->getParameter('catalog_img') . $entity->getTag());
            $finder->sortByName();

            return $this->render(
                'SpbShopBundle:Catalog:product.html.twig',
                array('entity' => $entity, 'parents'=>$parents, 'finder'=>$finder)
            );
        }

    }
}
