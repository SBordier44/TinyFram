<?php

namespace Tests\Framework;

use Framework\Upload;
use PHPUnit\Framework\TestCase;
use Psr\Http\Message\UploadedFileInterface;

class UploadTest extends TestCase
{
	
	/**
	 * @var Upload
	 */
	private $upload;
	
	public function setUp()
	{
		$this->upload = new Upload('tests');
	}
	
	public function tearDown()
	{
		if (file_exists('tests/demo.jpg')) {
			unlink('tests/demo.jpg');
		}
	}
	
	public function testUpload()
	{
		$uploadedFile = $this->getMockBuilder(UploadedFileInterface::class)->getMock();
		$uploadedFile->expects(static::any())->method('getError')->willReturn(UPLOAD_ERR_OK);
		$uploadedFile->expects(static::any())->method('getClientFilename')->willReturn('demo.jpg');
		$uploadedFile->expects(static::once())->method('moveTo')->with(static::equalTo('tests/demo.jpg'));
		static::assertEquals('demo.jpg', $this->upload->upload($uploadedFile));
	}
	
	public function testDontMoveIfFileNotUploaded()
	{
		$uploadedFile = $this->getMockBuilder(UploadedFileInterface::class)->getMock();
		$uploadedFile->expects(static::any())->method('getError')->willReturn(UPLOAD_ERR_CANT_WRITE);
		$uploadedFile->expects(static::any())->method('getClientFilename')->willReturn('demo.jpg');
		$uploadedFile->expects(static::never())->method('moveTo')->with(static::equalTo('tests/demo.jpg'));
		static::assertNull($this->upload->upload($uploadedFile));
	}
	
	public function testUploadWithExistingFile()
	{
		$uploadedFile = $this->getMockBuilder(UploadedFileInterface::class)->getMock();
		
		$uploadedFile->expects(static::any())->method('getError')->willReturn(UPLOAD_ERR_OK);
		$uploadedFile->expects(static::any())->method('getClientFilename')->willReturn('demo.jpg');
		
		touch('tests/demo.jpg');
		$uploadedFile->expects(static::once())->method('moveTo')->with(static::equalTo('tests/demo_copy.jpg'));
		
		static::assertEquals('demo_copy.jpg', $this->upload->upload($uploadedFile));
	}
	
	public function testDoNothingIfFileNotUploaded()
	{
		$file = $this->getMockBuilder(UploadedFileInterface::class)->disableOriginalConstructor()->getMock();
		$file->method('getError')->willReturn(UPLOAD_ERR_CANT_WRITE);
		$file->expects(static::never())->method('moveTo');
		$this->upload->upload($file);
	}
	
	public function testCreateFormats()
	{
		@unlink('tests/demo.png');
		@unlink('tests/demo_thumb.png');
		$file = $this->getMockBuilder(UploadedFileInterface::class)->disableOriginalConstructor()->getMock();
		$file->method('getError')->willReturn(UPLOAD_ERR_OK);
		$file->method('getClientFileName')->willReturn('demo.png');
		$file->expects(static::once())->method('moveTo')->willReturnCallback(function () {
			imagepng(imagecreatetruecolor(1000, 1000), 'tests/demo.png');
		});
		$property = (new \ReflectionClass($this->upload))->getProperty('formats');
		$property->setAccessible(true);
		$property->setValue($this->upload, ['thumb' => [100, 200]]);
		$this->upload->upload($file);
		static::assertArraySubset([100, 200], getimagesize('tests/demo_thumb.png'));
		static::assertFileExists('tests/demo_thumb.png');
		@unlink('tests/demo.png');
		@unlink('tests/demo_thumb.png');
	}
	
	public function testDeleteOldFormat()
	{
		$property = (new \ReflectionClass($this->upload))->getProperty('formats');
		$property->setAccessible(true);
		$property->setValue($this->upload, ['thumb' => [100, 200]]);
		touch('tests/demo.png');
		touch('tests/demo_thumb.png');
		$this->upload->delete('demo.png');
		static::assertFileNotExists('tests/demo.png');
		static::assertFileNotExists('tests/demo_thumb.png');
		@unlink('tests/demo.png');
		@unlink('tests/demo_thumb.png');
	}
}
