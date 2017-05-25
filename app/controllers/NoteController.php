<?php

use Phalcon\Mvc\Controller;

class NoteController extends Controller
{
    public static $video   = ['.mkv', '.flv', '.vob', '.ogg', '.ogv', '.avi', '.asf', '.mov', '.qt', '.swf', '.mpg', '.mp4', '.wmv', '.mpeg'];

    public static $music   = ['.webm', '.pcm', '.wav', '.aiff', '.mp3', '.aac', '.wma', '.flac', '.alac', '.3gp'];

    public static $images  = ['.gif', '.png', '.jpg', '.jpeg', '.bmp', '.pcx', '.webp', '.svg', '.tiff'];

    public static $doc     = ['.doc', '.docx', '.txt', '.odt', '.pdf', '.rtf', '.djvu', '.epub'];

    public static $archive = ['.rar', '.zip', '.7z', '.tar', '.iso', '.msi'];

    public function createAction()
    {
        if ($this->request->isPost()) {
            $form = new NoteForm();
            $error = [];

            if ($form->isValid($this->request->getPost())) {

                $expansion = 0;
                $src = 'default';
                $pattern = '#\.[a-z]*$#';
                preg_match($pattern, $this->request->getPost('titleFile'), $expansion);

                if (in_array($expansion, NoteController::$doc)) $src = '';
                elseif (in_array($expansion, NoteController::$images)) $src = '';
                elseif (in_array($expansion, NoteController::$music)) $src = '';
                elseif (in_array($expansion, NoteController::$video)) $src = '';
                elseif (in_array($expansion, NoteController::$archive)) $src = '';


                /**
                 * @var Note $note
                 */
                $note = new Note();
                $note->setUser($this->session->get('user_identity')['id']);
                $note->setText(nl2br($this->request->getPost('desc')));
                $note->setFileName($this->request->getPost('titleFile'));
                $note->setFile($this->session->get('md5_cache_file')['name']);
                $note->setExpansion($src);
                
                if ($note->save()) {
//                    $this->session->destroy('md5_cache_file');
                    return $this->response->setJsonContent(
                        [
                            'note' => $note
                        ]
                    );
                }
            } else {
                foreach ($form->getMessages() as $message)
                    $error[] = [
                        'field' => $message->getField(),
                        'message' => $message->getMessage()
                    ];

            }
            $this->response->setJsonContent([
                'error' => $error
            ]);
            return $this->response;
        }
    }
}