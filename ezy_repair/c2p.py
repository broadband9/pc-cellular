
import os

def format_folder_with_contents(folder_path, max_content_length=100000):
    """
    Reads a folder and formats its structure, including file names and their contents.
    
    Parameters:
        folder_path (str): The path of the folder to read.
        max_content_length (int): Maximum number of characters to include from each file's contents.
        
    Returns:
        str: A formatted string representing the folder's structure and file contents.
    """
    def traverse_directory(path, level=0):
        formatted_output = ""
        indent = "  " * level  # Indentation for hierarchical structure

        # List all items in the directory
        try:
            items = os.listdir(path)
        except PermissionError:
            return f"{indent}[Permission Denied]\n"

        for item in sorted(items):  # Sort items alphabetically
            item_path = os.path.join(path, item)

            if os.path.isdir(item_path):
                formatted_output += f"{indent}- Folder: {item}\n"
                formatted_output += traverse_directory(item_path, level + 1)
            elif os.path.isfile(item_path):
                formatted_output += f"{indent}- File: {item}\n"
                try:
                    with open(item_path, 'r', encoding='utf-8') as file:
                        content = file.read()
                        if len(content) > max_content_length:
                            content = content[:max_content_length] + "... [Content Truncated]"
                        formatted_output += f"{indent}  Contents:\n{indent}  {content.replace('\n', '\n' + indent + '  ')}\n"
                except Exception as e:
                    formatted_output += f"{indent}  [Error Reading File: {e}]\n"

        return formatted_output

    if not os.path.isdir(folder_path):
        return "Error: Provided path is not a directory."

    folder_structure = f"Root Folder: {os.path.basename(folder_path)}\n"
    folder_structure += traverse_directory(folder_path)
    return folder_structure


# Example usage
if __name__ == "__main__":
    folder_path = input("Enter the folder path: ").strip()
    folder_structure = format_folder_with_contents(folder_path)
    print("Formatted Folder Structure with File Contents for LLM Prompt:\n")
    print(folder_structure)

    # Optionally, save the output to a text file
    output_file = "folder_structure_with_contents.txt"
    with open(output_file, "w", encoding="utf-8") as file:
        file.write(folder_structure)
    print(f"\nThe folder structure with contents has been saved to {output_file}.")
