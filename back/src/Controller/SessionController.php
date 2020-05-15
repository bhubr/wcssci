<?php
/**
 * Created by PhpStorm.
 * User: root
 * Date: 11/10/17
 * Time: 16:07
 * PHP version 7
 */

namespace App\Controller;

use App\Model\ResourceManager;
use App\Model\SessionManager;

/**
 * Class SessionController
 *
 */
class SessionController extends AbstractController
{

    /**
     * Display item listing
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function index()
    {
        $sessionManager = new sessionManager();
        $sessions = $sessionManager->selectAll();

        return json_encode($sessions);
    }

    /**
     * Display item informations specified by $id
     *
     * @param int $id
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function get(int $id)
    {
        $sessionManager = new sessionManager();
        $session = $sessionManager->selectOneById($id);

        return json_encode($session);
    }

    /**
     * Display item edition page specified by $id
     *
     * @param int $id
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function update(int $sessionId): string
    {
        $sessionManager = new sessionManager();
        $session = $sessionManager->selectOneById($sessionId);

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            header('HTTP/1.1 405 Method Not Allowed');
            exit;
        }

        $session['title'] = $this->jsonInput['title'];
        $session['description'] = $this->jsonInput['description'];
        $session['language'] = $this->jsonInput['language'];
        $session['created_at'] = $this->jsonInput['createdAt'];
        $sessionManager->update($session);

        $resourceManager = new ResourceManager();

        $resources = array_map(function ($resourceData) use ($resourceManager, $sessionId) {
            if (!isset($resourceData['session_id'])) {
                $resourceData['session_id'] = $sessionId;
                $resourceId = $resourceManager->insert($resourceData);
                $resource = $resourceData;
                $resource['id'] = $resourceId;
                return $resource;
            } else {
                $resourceManager->update($resourceData);
                return $resourceData;
            }
        }, $this->jsonInput['resources']);
        $session['resources'] = $resources;

        header('Content-Type: application/json');
        return json_encode($session);
    }

    /**
     * Display item creation page
     *
     * @return string
     * @throws \Twig\Error\LoaderError
     * @throws \Twig\Error\RuntimeError
     * @throws \Twig\Error\SyntaxError
     */
    public function create()
    {

        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $sessionManager = new SessionManager();
            $session = [
                'title' => $this->jsonInput['title'],
                'description' => $this->jsonInput['description'],
                'language' => $this->jsonInput['language'],
                'created_at' => $this->jsonInput['createdAt'],
            ];
            $sessionId = $sessionManager->insert($session);
            $session['id'] = $sessionId;

            $resourceManager = new ResourceManager();

            $resources = array_map(function ($resourceData) use ($resourceManager, $sessionId) {
                $resourceData['session_id'] = $sessionId;
                $resourceId = $resourceManager->insert($resourceData);
                $resource = $resourceData;
                $resource['id'] = $resourceId;
                return $resource;
            }, $this->jsonInput['resources']);
            $session['resources'] = $resources;

            $protocol = (isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1');

            header($protocol . ' 201 Created');
            return json_encode($session);
        }

        return $this->twig->render('Item/add.html.twig');
    }

    /**
     * Handle item deletion
     *
     * @param int $id
     */
    public function delete(int $id)
    {
        $sessionManager = new sessionManager();
        $sessionManager->delete($id);
        header('Location:/item/index');
    }
}
