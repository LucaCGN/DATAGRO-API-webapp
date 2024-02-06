import sys
import pkg_resources

print("Python Version:", sys.version)
print("\nInstalled Packages:")
installed_packages = [(d.project_name, d.version) for d in pkg_resources.working_set]
for package_name, version in sorted(installed_packages):
    print(f"{package_name}=={version}")
