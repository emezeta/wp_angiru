// save.js
import { RichText } from '@wordpress/block-editor';

const save = (props) => {
  const { attributes } = props;
  const { image } = attributes;

  return (
    <div>
      {image && <img src={image.url} alt="Selected Image" />}
      <RichText.Content tagName="p" value={image? image.url : ''} />
    </div>
  );
};

export default save;