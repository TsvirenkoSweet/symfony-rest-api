<?php

namespace App\Controller;


use App\Entity\Product;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\Serializer;

/**
 * @Route("/products")
 */

class ProductController extends AbstractController
{
    /**
     * @Route("/{page}", name="product_list", defaults={"page": 1}, requirements={"page"="\d+"})
     */
    public function productList($page = 1, Request $request)
    {
        $limit = $request->get('limit', 10);
        $repository = $this->getDoctrine()->getRepository(Product::class);
        $items = $repository->findAll();
        return $this->json(
            [
                'page' => $page,
                'limit' => $limit,
                'dataJson' => $items,
                'dataUrlById' => array_map(function ($item){
                    return $this->generateUrl('product_by_id', ['id' => $item->getId()]);
                }, $items),
                'dataUrlBySlug' => array_map(function ($item){
                    return $this->generateUrl('product_by_slug', ['slug' => $item->getSlug()]);
                }, $items)
            ]
        );
    }

    /**
     * @Route("/product/{id}", name="product_by_id", requirements={"id"="\d+"}, methods={"GET"})
     * @ParamConverter("product", class="App:Product")
     */
    public function product($product)
    {
        return $this->json($product);
    }

    /**
     * @Route("/product/{slug}", name="product_by_slug", methods={"GET"})
     * @ParamConverter("product", class="App:Product", options={"mapping": {"slug": "slug"}})
     */
    public function productSlug($product)
    {
        return $this->json($product);
    }

    /**
     * @Route("/add", name="product_add", methods={"POST"})
     */
    public function add(Request $request)
    {
        /** @var Serializer $serializer */
        $serializer = $this->get('serializer');
        $productPost = $serializer->deserialize($request->getContent(), Product::class, 'json');

        $em = $this->getDoctrine()->getManager();
        $em->persist($productPost);
        $em->flush();

        return $this->json($productPost);
    }

    /**
     * @Route("/product/{id}", name="product_delete", methods={"DELETE"})
     */
    public function delete(Product $request)
    {
        $em = $this->getDoctrine()->getManager();
        $em->remove($request);
        $em->flush();

        return new JsonResponse(null, Response:: HTTP_NO_CONTENT);
    }
}