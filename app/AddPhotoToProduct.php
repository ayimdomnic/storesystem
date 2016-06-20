<?php

namespace App;

use Symphony\Component\HttpFoundation\File\UploadFile;

class AddPhotoToProduct 
{

	protected $product



	protected $file




	public function __construct(Product $product, UploadFile $file, Thumbnail $thumbnail = null)
	{
		$this->product = $product;
		$this->file =$file;
		$this->thumbnail = $thumbnail ?: new Thumbnail;
	}


	public function save()
	{
		$photo = $this->product->addPhoto($this->makePhoto());

		$this->file->move($photo->baseDir(), $photo->name);
		$this->thumbnail->make($photo->path, $photo->thumbnail_path);
	}

	#make a new photo instance
	//pardon my comments I'm using Sublime text

	protected function makePhoto()
	{
		return new ProductPhoto(['name'=> $this->makeFilename()]);
	}



	protected function makeFilename()
	{
		//get the files original name
		//sha1 encryption
		$name = sha1(
			time() . $this->file->getClientOriginalName();
			);
		//get the extension of the photo
		$extension = $this->file->getClientOriginalExtension();

		// then set name we merge the two (Dom The GOAT)
		 return "{$name}.{$extension}";
	}
    
}
