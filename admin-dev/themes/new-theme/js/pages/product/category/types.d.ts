export interface Category {
  id: number,
  name: string,
  displayName: string,
}

export interface TreeCategory {
  id: number,
  name: string,
  displayName: string,
  active: boolean,
  children: Array<TreeCategory>,
}
