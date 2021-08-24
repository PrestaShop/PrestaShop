type Category = {
  id: number,
  name: string,
  isDefault: boolean,
}

type TypeaheadCategory = {
  id: number,
  name: string,
  breadcrumb: string,
  children: Array<TypeaheadCategory>
}

type TreeCategory = {
  id: number,
  name: string,
  active: boolean,
  children: Array<TreeCategory>,
}
