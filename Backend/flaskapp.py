from flask import Flask, request, jsonify
from model import CNN 
import numpy as np
import cv2
from utils import preprocess_image  # Import the preprocess_image function from utils.py
import torch

app = Flask(__name__)

model_path = '/Applications/XAMPP/xamppfiles/htdocs/final/models/model.pth'

# Instantiate the model architecture
model = CNN() 

# Load the state dictionary from the file
state_dict = torch.load(model_path)


# Load the state dictionary into the model
model.load_state_dict(state_dict)
# Load your trained model here
#model = torch.load('/Applications/XAMPP/xamppfiles/htdocs/final/models/model.pth')
# Set the model to evaluation mode
model.eval()


@app.route('/process_image', methods=['POST'])
def process_image():
    # Check if there is an image file in the request
    if 'image' not in request.files:
        return jsonify({'error': 'No image uploaded'}), 400

    # Retrieve the image file from the request
    image_file = request.files['image']

    # Read the image file as a NumPy array
    image_np = cv2.imdecode(np.frombuffer(image_file.read(), np.uint8), cv2.IMREAD_COLOR)

    # Check if the image was properly decoded
    if image_np is None:
        return jsonify({'error': 'Invalid image data'}), 400

    # Preprocess the image
    preprocessed_image = preprocess_image(image_np)

    # Make predictions using the model
    with torch.no_grad():
        output = model(preprocessed_image)

    # Convert model output to a probability using sigmoid
    prediction = output.item()
    print(prediction)
    # Return the prediction as a JSON response
    return jsonify({'prediction': prediction})


if __name__ == '__main__':
    app.run(debug=True)
