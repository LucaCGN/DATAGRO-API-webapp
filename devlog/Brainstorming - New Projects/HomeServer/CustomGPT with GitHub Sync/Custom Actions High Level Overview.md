''

1. **Fetch File Contents Action**:
   - **OperationId**: `fetchFileContents`
   - **Endpoint**: `GET /repos/{owner}/{repo}/contents/{path}`
   - **Purpose**: Retrieve the contents of a specific file in the repository.
   - **Parameters**: Owner, Repo, Path, Ref (optional, for specifying a branch or tag).

2. **Fetch Directory Contents Action**:
   - **OperationId**: `fetchDirectoryContents`
   - **Endpoint**: Same as above.
   - **Purpose**: Retrieve the contents of an entire directory (up to 1,000 files).
   - **Parameters**: Same as above, but `path` would point to a directory.

3. **Fetch Repository Structure Action**:
   - **OperationId**: `fetchRepositoryStructure`
   - **Endpoint**: `GET /repos/{owner}/{repo}/git/trees/{tree_sha}`
   - **Purpose**: Get a tree structure of the repository to understand its layout and file organization.
   - **Parameters**: Owner, Repo, Tree SHA (or branch/tag reference), Recursive (optional).

4. **Fetch Large File Contents Action**:
   - **OperationId**: `fetchLargeFileContents`
   - **Endpoint**: Same as the first action.
   - **Purpose**: Specifically tailored for files larger than 1MB and up to 100MB.
   - **Parameters**: Same as `fetchFileContents`, but with additional handling for large files.

