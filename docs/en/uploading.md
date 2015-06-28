# Uploading media files

For [uploading](https://www.tumblr.com/docs/en/api/v2#posts) media files like photos, videos and music is available special method in Tumblr client.

## Uploading

Upload is simple done with this call:

```php
class YourAppSomePresenter extends BasePresenter
{
	/**
	 * @var \IPub\Tumblr\Client
	 */
	protected $tumblr;

	public function actionUpload()
	{
		try {
			$mediaData = $this->tumblr->uploadMedia('blogname.tumblr.com', 'photo', 'full/absolute/path/to/your/image.jpg');

		} catch (\IPub\OAuth\ApiException $ex) {
			// something went wrong
		}
	}
}
```

If upload is successful an blog post id is returned, in other case an exception will be thrown.

Basic params for this method are:

* **Blog name** - your blog name in full format like myThumblrName.tumblr.com
* **Media type** - type of uploaded media - allowed strings are: photo, video, audio
* **Image path** - full path to image file
* **Params** - Additional params to be posted with media file. For [photo](https://www.tumblr.com/docs/en/api/v2#photo-posts), for [video](https://www.tumblr.com/docs/en/api/v2#video-posts) and for [audio](https://www.tumblr.com/docs/en/api/v2#audio-posts)
