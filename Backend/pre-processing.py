import torch
import cv2
import numpy as np

def preprocess_image(image):
    image = cv2.cvtColor(image, cv2.COLOR_BGR2RGB)  # Convert image to RGB
    image = cv2.resize(image, (128, 128))  # Resize image to model input size
    image = image / 255.0  # Normalize pixel values to [0, 1]
    image = np.transpose(image, (2, 0, 1))  # Transpose image array to (channels, height, width)
    image = torch.tensor(image, dtype=torch.float32)  # Convert image to PyTorch tensor
   
    return image.unsqueeze(0)  # Add batch dimension
