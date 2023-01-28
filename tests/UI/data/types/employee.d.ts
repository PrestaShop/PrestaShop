type EmployeeCreator = {
  id?: number
  firstName?: string
  lastName?: string
  email?: string
  password?: string
  defaultPage?: string
  language?: string
  active?: boolean
  permissionProfile?: string
  avatarFile?: string|null
  enableGravatar?: boolean
};

export default EmployeeCreator;
