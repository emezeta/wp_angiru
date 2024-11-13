// edit.js
import { RichText, InspectorControls, MediaUpload, MediaUploadCheck } from '@wordpress/block-editor';
import { Button } from '@wordpress/components';

const Edit = () => {
  const [image, setImage] = useState(null);

  const onSelectImage = (image) => {
    setImage(image);
  };

  return (
    <div>
      <InspectorControls>
        <MediaUploadCheck>
          <MediaUpload
            onSelect={onSelectImage}
            allowedTypes={["image"]}
            value={image? image.id : 0}
            render={({ open }) => (
              <Button onClick={open}>Select Image</Button>
            )}
          />
        </MediaUploadCheck>
      </InspectorControls>
      <RichText
        tagName="p"
        value={image? image.url : ''}
        onChange={(newText) => setImage({ url: newText })}
        placeholder="Enter text or select an image"
      />
    </div>
  );
};

export default Edit;