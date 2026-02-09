# COPILOT DOCUMENTATION

This directory contains additional documentation created by GitHub Copilot agents for modifications made to the PrestaShop-Entre repository. This documentation supplements PrestaShop's official documentation without modifying it.

## Purpose

Per project requirements, all additional documentation created by Copilot agents is stored in this separate folder to:
1. Preserve PrestaShop's official documentation in its original state
2. Provide comprehensive documentation for Copilot-driven changes
3. Maintain clear separation between official and supplementary documentation
4. Reference specific files, directories, and code lines related to changes

## Documentation Files

### QUICK_REFERENCE.md
**Topic**: Quick verification guide for Docker volumes fix  
**Summary**: Fast reference with verification commands and what to expect after the fix. Use this for quick validation.

### DOCKER_VOLUMES_FIX.md
**Topic**: Docker Compose Named Volumes Configuration Fix  
**Related Files**: `/docker-compose.yml`  
**Summary**: Documents the removal of named volumes (ps-var, ps-img, ps-upload, ps-download) that were preventing PrestaShop from serving images and files correctly in the development environment.

**Key Changes**:
- Lines 3-6: Removed named volume declarations (ps-var, ps-img, ps-upload, ps-download)
- Lines 62-65: Removed named volume mounts from prestashop-git service

**Why This Matters**: Named volumes were overlaying the bind mount, isolating images and uploads from the local filesystem, which broke image serving in both front and back office.

### WINDOWS_BATCH_SCRIPTS.md
**Topic**: Windows Batch Scripts for Docker Compose Management  
**Related Files**: `/quick-start.bat`, `/start.bat`, `/stop.bat`, `/restart.bat`, `/docker-compose.yml`  
**Summary**: Documents the Windows batch scripts created to manage PrestaShop Docker development environment. These scripts replace outdated scripts that referenced the incorrect "admin-fast" folder and now properly reference "admin-dev".

**Key Features**:
- `quick-start.bat`: Quick start for daily development (fast)
- `start.bat`: Build and start Docker containers (full setup)
- `stop.bat`: Stop Docker containers gracefully
- `restart.bat`: Rebuild and restart containers
- Correct admin folder reference: `admin-dev` (not `admin-fast`)
- Native Windows support without requiring Make

**Why This Matters**: Provides Windows users with simple, native batch commands to manage Docker Compose services, correctly pointing to the current admin folder structure.

## How to Use This Documentation

1. **Finding Relevant Documentation**: Each document clearly states which files it relates to
2. **Understanding Changes**: Documents include before/after code examples with line numbers
3. **Verification**: Step-by-step instructions for verifying the changes work correctly
4. **Troubleshooting**: Common issues and solutions are documented

## Documentation Standards

All documentation in this folder follows these standards:
- **Comprehensive**: Covers the what, why, and how of each change
- **Referenced**: Includes specific file paths and line numbers
- **Technical**: Provides technical details and rationale
- **Practical**: Includes verification and troubleshooting steps
- **Respectful**: Never modifies PrestaShop's official documentation

## Contributing

When adding new documentation to this folder:
1. Use descriptive filenames (e.g., `FEATURE_DESCRIPTION.md`)
2. Include "Related Files" section with absolute paths
3. Specify exact line numbers for code changes
4. Provide before/after examples
5. Include verification steps
6. Add troubleshooting guidance
7. Update this README with a summary of the new document

## PrestaShop Official Documentation

PrestaShop's official documentation remains in its original locations:
- `/docs/`: Core developer documentation
- `/INSTALL.txt`: Installation instructions
- `/README.md`: Main repository README
- `/CONTRIBUTING.md`: Contribution guidelines
- Various `/README.md` files in subdirectories

**Do not modify these files**. All supplementary documentation belongs in this directory.

## Questions or Issues

If you have questions about any documentation in this folder or need clarification on changes:
1. Review the specific documentation file
2. Check the "Related Files" section for the affected code
3. Follow the "Verification Steps" to test the changes
4. Consult the "Troubleshooting" section for common issues

## Version History

This folder was created to maintain separation between official PrestaShop documentation and Copilot agent modifications, ensuring that the original documentation remains pristine while still providing comprehensive documentation for all changes made.
