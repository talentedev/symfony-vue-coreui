<?php


namespace App\Service;

use App\Exceptions\WebException;
use Carbon\Carbon;
use Nette\Utils\DateTime;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Templating\EngineInterface;

class MailService
{
    /**
     * @var \Swift_Mailer $mailer
     */
    private $mailer;

    /**
     * @var EngineInterface $templating
     */
    private $templating;

    /**
     * MailService constructor.
     * @param \Swift_Mailer $mailer
     * @param EngineInterface $templating
     */
    public function __construct(\Swift_Mailer $mailer, EngineInterface $templating)
    {
        $this->mailer = $mailer;
        $this->templating = $templating;
    }

    /**
     * @param string $subject
     * @param string $from
     * @param string $to
     * @param string $path
     * @param mixed[] $params
     * @throws WebException
     * @return int
     */
    public function send(string $subject, string $from, string $to, string $path, array $params) : int
    {
        $content = new \Swift_Message();

        if (!$this->templating->exists($path)) {
            throw new WebException('Error : The template at this path '.$path.' doesn\'t exist.');
        }

        $WebLogo = $content->embed(\Swift_Image::fromPath('image/brand/Web_logo.png'));
        $params['src_logo'] = $WebLogo;

        $content
            ->setSubject($subject)
            ->setFrom($from)
            ->setTo([$to])
            ->setBody(
                $this->templating->render(
                    $path,
                    $params
                ),
                'text/html'
            )->setCharset('utf-8');
        return $this->mailer->send($content, $error);
    }
}
