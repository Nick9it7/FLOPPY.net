<?php

use Phalcon\Mvc\Controller;

class NoteController extends Controller
{
    public static $video   = ['.mkv', '.flv', '.vob', '.ogg', '.ogv', '.avi', '.asf', '.mov', '.qt', '.swf', '.mpg', '.mp4', '.wmv', '.mpeg'];

    public static $doc     = ['.doc', '.docx', '.xls', '.xlsx', '.ppt', '.pptx', '.txt', '.odt', '.pdf', '.rtf', '.djvu', '.epub'];

    public static $music   = ['.webm', '.pcm', '.wav', '.aiff', '.mp3', '.aac', '.wma', '.flac', '.alac', '.3gp'];

    public static $images  = ['.gif', '.png', '.jpg', '.jpeg', '.bmp', '.pcx', '.webp', '.svg', '.tiff'];

    public static $archive = ['.rar', '.zip', '.7z', '.tar', '.iso', '.msi'];

    public static $exe = ['.exe'];

    public function createAction()
    {
        if ($this->request->isPost()) {
            $form = new NoteForm();
            $error = [];

            if ($form->isValid($this->request->getPost())) {

                $expansion = 0;
                $src = '/public/img/format/default.png';
                $pattern = '#\.[0-9a-z]*$#i';
                preg_match($pattern, $this->request->getPost('titleFile'), $expansion);
                $expansion = strtolower($expansion[0]);

                if (in_array($expansion, NoteController::$doc)) $src = '/public/img/format/doc.png';
                elseif (in_array($expansion, NoteController::$images)) $src = '/public/img/format/pictures.png';
                elseif (in_array($expansion, NoteController::$music)) $src = '/public/img/format/music.png';
                elseif (in_array($expansion, NoteController::$video)) $src = '/public/img/format/video.png';
                elseif (in_array($expansion, NoteController::$archive)) $src = '/public/img/format/archive.png';
                elseif (in_array($expansion, NoteController::$exe)) $src = '/public/img/format/exe.png';


                $post = htmlspecialchars(nl2br($this->request->getPost('desc')));
                $post = str_replace('&lt;br /&gt;' , '<br>', $post);
                /**
                 * @var Note $note
                 */
                $note = new Note();
                $note->setUser($this->session->get('user_identity')['id']);

                $note->setText($post);
                $note->setFileName($this->request->getPost('titleFile'));
                $note->setFile('/user_' . $this->session->get('user_identity')['id'] . '/' . $this->session->get('cache_file')['name']);
                $note->setExpansion($src);
                
                if ($note->save()) {
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